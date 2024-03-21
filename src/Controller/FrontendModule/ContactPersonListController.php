<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\Controller\FrontendModule;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Routing\ResponseContext\JsonLd\JsonLdManager;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\NewsModel;
use Contao\StringUtil;
use Contao\Template;
use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\ContactPersonRelPageModel;
use numero2\ContactPersonsBundle\Event\ContactPersonEvents;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\NewsArticle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @FrontendModule("contact_person_list",
 *   category="contactPersons",
 *   template="mod_contact_person_list",
 * )
 */
class ContactPersonListController extends AbstractFrontendModuleController {


    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor
     */
    private $responseContextAccessor;

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $supportedPageTypes;


    public function __construct( EventDispatcherInterface $eventDispatcher, ResponseContextAccessor $responseContextAccessor, TranslatorInterface $translator ) {

        $this->eventDispatcher = $eventDispatcher;
        $this->responseContextAccessor = $responseContextAccessor;
        $this->translator = $translator;

        $this->supportedPageTypes = [];

        if( class_exists(CalendarEventsModel::class) ) {
            $this->supportedPageTypes[Event::class] = CalendarEventsModel::getTable();
        }
        if( class_exists(NewsModel::class) ) {
            $this->supportedPageTypes[NewsArticle::class] = NewsModel::getTable();
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function getResponse( Template $template, ModuleModel $model, Request $request ): ?Response {

        $contacts = [];

        $options = [];

        if( $model->numberOfItems >= 0 ) {
            $options['limit'] = $model->numberOfItems;
        }

        $sorting = StringUtil::deserialize($model->contact_person_sorting, true);
        if( strlen($sorting['value']) ) {
            $options['order'] = $sorting['value'] . ' ' . $sorting['unit'];
        }

        if( $model->contact_persons !== null ) {

            $contactIds = StringUtil::deserialize($model->contact_persons, true);

            if( !empty($contactIds) ) {

                $oContacts = ContactPersonModel::findMultiplePublishedById($contactIds, $options);

                if( $oContacts ) {
                    foreach( $oContacts as $oContact ) {
                        $contacts[] = $oContact->row();
                    }
                }
            }

        } else {

            $contactIds = [];
            $page = $this->getPageModel();
            $table = $page::getTable();

            $relations = ContactPersonRelPageModel::findBy(['page_table=? AND page_id=?'], [$table, $page->id]);
            if( $relations ) {
                $contactIds = array_merge($relations->fetchEach('pid'), $contactIds);
            }

            $schemaManager = $this->responseContextAccessor->getResponseContext()->get(JsonLdManager::class);
            $graph = $schemaManager->getGraphForSchema(JsonLdManager::SCHEMA_ORG);
            $nodes = $graph->getNodes();

            foreach( $this->supportedPageTypes as $cls => $table ) {
                if( is_array($nodes[$cls] ?? null) ) {
                    foreach( $nodes[$cls] as $identifier => $data ) {

                        // \Contao\News => 'identifier' => '#/schema/news/' . $objArticle->id,
                        // \Contao\Event => 'identifier' => '#/schema/events/' . $objEvent->id,

                        $chunks = explode('/', $identifier);

                        if( !empty($chunks[3]) ) {

                            $pageId = intval($chunks[3]);

                            $relations = ContactPersonRelPageModel::findBy(['page_table=? AND page_id=?'], [$table, $pageId]);
                            if( $relations ) {
                                $contactIds = array_merge($relations->fetchEach('pid'), $contactIds);
                            }
                        }
                    }
                }
            }

            // inherit if configured and none found
            if( !empty($model->contact_persons_inherit) && empty($contactIds) ) {

                if( !empty($page->trail) ) {

                    // go upwards
                    $trail = array_reverse($page->trail);
                    // current page already done
                    array_shift($trail);

                    $table = $page::getTable();

                    foreach( $trail as $pageId ) {

                        $relations = ContactPersonRelPageModel::findBy(['page_table=? AND page_id=?'], [$table, $pageId]);
                        if( $relations ) {
                            $contactIds = array_merge($relations->fetchEach('pid'), $contactIds);
                            break;
                        }
                    }
                }
            }

            if( !empty($contactIds) ) {

                $contactIds = array_unique($contactIds);
                $oContacts = ContactPersonModel::findMultiplePublishedById($contactIds, $options);

                if( $oContacts ) {
                    foreach( $oContacts as $oContact ) {
                        $contacts[] = $oContact->row();
                    }
                }
            }
        }

        if( empty($contacts) ) {
            return new Response('');
        }

        // parse entries
        foreach( $contacts as $key => $contact ) {

            $event = new ContactPersonParseEvent($contact, $model);
            $this->eventDispatcher->dispatch($event, ContactPersonEvents::CONTACT_PERSON_PARSE);

            $contact = $event->getContactPerson();

            $contactTemplate = new FrontendTemplate($model->contact_person_template ?: 'contact_person_default');
            $contactTemplate->setData($contact);

            $contacts[$key] = $contactTemplate->parse();
        }

        $template->contacts = $contacts;

        $template->empty = $this->translator->trans('MSC.contact_person_list.empty', [], 'contao_default');

        return $template->getResponse();
    }


    /**
     * Generate a tel href from the given number and protocol
     *
     * @param string $number
     * @param string $protocol
     *
     * @return string
     */
    private function genereateTelHref( string $number, string $protocol='tel' ): string {

        return $protocol.':'. preg_replace("|[^\+0-9]|", "", $number);
    }
}
