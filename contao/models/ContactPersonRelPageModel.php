<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle;

use Contao\Model;


class ContactPersonRelPageModel extends Model {


    /**
     * @var string
     */
    protected static $strTable = 'tl_contact_person_rel_pages';
}