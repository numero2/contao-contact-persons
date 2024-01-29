<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\ContactPersonRelPageModel;


/**
 * MODELS
 */
$GLOBALS['TL_MODELS'][ContactPersonModel::getTable()] = ContactPersonModel::class;
$GLOBALS['TL_MODELS'][ContactPersonRelPageModel::getTable()] = ContactPersonRelPageModel::class;


/**
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['contactPersons'] = [
    'tables' => [ContactPersonModel::getTable(), ContactPersonRelPageModel::getTable()]
];
