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


/**
 * Modify the palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['contact_person'] = '{title_legend},type,headline;{contact_persons_legend},contact_person;{image_legend:hide},size;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';


/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['contact_person'] = [
    'exclude'               => true
,   'inputType'             => 'picker'
,   'foreignKey'            => ContactPersonModel::getTable().'.lastname'
,   'eval'                  => ['mandatory'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr']
,   'sql'                   => "int(10) unsigned NOT NULL default 0"
,   'relation'              => ['type'=>'hasOne', 'load'=>'lazy']
];