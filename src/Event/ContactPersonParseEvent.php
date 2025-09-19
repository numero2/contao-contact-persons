<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\Event;

use Contao\Model;
use Contao\PageModel;
use Symfony\Contracts\EventDispatcher\Event;


class ContactPersonParseEvent extends Event {


    /**
     * @var array
     */
    private array $contact;

    /**
     * @var Contao\Model
     */
    private Model $model;

    /**
     * @var Contao\PageModel|null
     */
    private PageModel|null $pageModel;


    public function __construct( array $contact, Model $model, ?PageModel $pageModel=null ) {

        $this->contact = $contact;
        $this->model = $model;
        $this->pageModel = $pageModel;
    }


    /**
     * @return array
     */
    public function getContactPerson(): array {

        return $this->contact;
    }


    /**
     * @param array $contact
     *
     * @return numero2\ContactPersonsBundle\Event\ContactPersonParseEvent
     */
    public function setContactPerson( array $contact ): self {

        $this->contact = $contact;

        return $this;
    }


    /**
     * @return Contao\Model
     */
    public function getModel(): Model {

        return $this->model;
    }


    /**
     * @return Contao\PageModel|null
     */
    public function getPageModel(): ?PageModel {

        return $this->pageModel;
    }
}
