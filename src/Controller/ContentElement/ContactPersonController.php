<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FrontendTemplate;
use Contao\Template;
use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\Event\ContactPersonEvents;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @ContentElement("contact_person",
 *   category="includes",
 *   template="ce_contact_person",
 * )
 */
class ContactPersonController extends AbstractContentElementController {


    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Contao\CoreBundle\Routing\ScopeMatcher
     */
    private $scopeMatcher;


    public function __construct( EventDispatcherInterface $eventDispatcher, ScopeMatcher $scopeMatcher ) {

        $this->eventDispatcher = $eventDispatcher;
        $this->scopeMatcher = $scopeMatcher;
    }


    /**
     * {@inheritdoc}
     */
    protected function getResponse( Template $template, ContentModel $model, Request $request ): ?Response {

        $oContact = ContactPersonModel::findPublishedById($model->contact_person);

        if( !$oContact ) {
            return new Response('');
        }

        $contact = $oContact->row();

        // parse data
        $event = new ContactPersonParseEvent($contact, $model);
        $this->eventDispatcher->dispatch($event, ContactPersonEvents::CONTACT_PERSON_PARSE);

        $contact = $event->getContactPerson();

        if( $this->scopeMatcher->isBackendRequest($request) ) {
            $model->contact_person_template = null;
        }

        $contactTemplate = new FrontendTemplate($model->contact_person_template ?: 'contact_person_default');
        $contactTemplate->setData($contact);

        $template->contact = $contactTemplate->parse();

        return $template->getResponse();
    }
}
