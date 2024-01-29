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


final class ContactPersonEvents {


    /**
     * The contao.contact_person_parse event is triggered during parsing a contact person entry.
     *
     * @see numero2\SpreadsheetCatalogBundle\Event\DataParseEvent
     */
    public const CONTACT_PERSON_PARSE = 'contao.contact_person_parse';
}
