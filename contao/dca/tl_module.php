<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


use numero2\ContactPersonsBundle\ContactPersonModel;


/**
 * Modify the palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['contact_person_list'] = '{title_legend},name,headline,type;{contact_persons_legend},contact_persons,contact_persons_inherit;{config_legend},numberOfItems,contact_person_sorting;{image_legend:hide},imgSize;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';


/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['contact_persons'] = [
    'exclude'               => true
,   'inputType'             => 'picker'
,   'foreignKey'            => ContactPersonModel::getTable().'.lastname'
,   'eval'                  => ['multiple'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr']
,   'sql'                   => "blob NULL"
,   'relation'              => ['type'=>'hasMany', 'load'=>'lazy']
];

$GLOBALS['TL_DCA']['tl_module']['fields']['contact_persons_inherit'] = [
    'exclude'               => true
,   'inputType'             => 'checkbox'
,   'eval'                  => ['tl_class'=>'clr w50']
,   'sql'                   => ['type'=>'boolean', 'default'=>false]
];

$GLOBALS['TL_DCA']['tl_module']['fields']['contact_person_sorting'] = [
    'exclude'               => true
,   'inputType'             => 'inputUnit'
,   'options'               => ['asc', 'desc']
,   'reference'             => &$GLOBALS['TL_LANG']['tl_module']['contact_person_sortings']
,   'eval'                  => ['maxlength'=>200, 'tl_class'=>'w50']
,   'sql'                   => "varchar(255) NOT NULL default 'a:2:{s:5:\"value\";s:0:\"\";s:4:\"unit\";s:3:\"asc\";}'"
];