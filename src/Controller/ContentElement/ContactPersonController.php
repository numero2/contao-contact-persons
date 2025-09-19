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
use Contao\ContentProxy;
use Contao\FrontendTemplate;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\Event\ContactPersonEvents;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


#[AsContentElement('contact_person', category: 'includes')]
class ContactPersonController extends AbstractContentElementController {


    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var Contao\CoreBundle\Routing\ScopeMatcher
     */
    private ScopeMatcher $scopeMatcher;


    public function __construct( EventDispatcherInterface $eventDispatcher, ScopeMatcher $scopeMatcher ) {

        $this->eventDispatcher = $eventDispatcher;
        $this->scopeMatcher = $scopeMatcher;
    }


    /**
     * {@inheritdoc}
     */
    protected function getResponse( FragmentTemplate $template, ContentModel $model, Request $request ): Response {

        $oContact = ContactPersonModel::findPublishedById($model->contact_person);

        if( !$oContact ) {
            return new Response('');
        }

        $contact = $oContact->row();

        // parse data
        $event = new ContactPersonParseEvent($contact, $model);
        $this->eventDispatcher->dispatch($event, ContactPersonEvents::CONTACT_PERSON_PARSE);

        $contact = $event->getContactPerson();

        $template->set('contact', $contact);

        return $template->getResponse();
    }
}
