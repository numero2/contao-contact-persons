<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\DataContainer;
use Contao\DC_Table;
use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\ContactPersonRelPageModel;


$GLOBALS['TL_DCA'][ContactPersonModel::getTable()] = [

    'config' => [
        'dataContainer'             => DC_Table::class
    ,   'ctable'                    => [ContactPersonRelPageModel::getTable()]
    ,   'enableVersioning'          => true
    ,   'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ]
,   'list' => [
        'sorting' => [
            'mode'                  => DataContainer::MODE_SORTABLE
        ,   'fields'                => ['lastname']
        ,   'flag'                  => DataContainer::SORT_INITIAL_LETTER_ASC
        ,   'panelLayout'           => 'filter;sort,search,limit'
        ]
    ,   'label' => [
            'fields'                => ['firstname', 'lastname', 'position', 'email']
        ,   'showColumns'           => true
        ]
    ,   'global_operations' => [
            'all' => [
                'href'              => 'act=select'
            ,   'class'             => 'header_edit_all'
            ,   'attributes'        => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ]
    ,   'operations' => [
            'edit' => [
                'href'              => 'act=edit'
            ,   'icon'              => 'edit.svg'
            ]
        ,   'delete' => [
                'href'              => 'act=delete'
            ,   'icon'              => 'delete.svg'
            ,   'attributes'        => 'onclick="if (!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\')) return false; Backend.getScrollOffset();"'
            ]
        ,   'toggle' => [
                'href'              => 'act=toggle&amp;field=published'
            ,   'icon'              => 'visible.svg'
            ,   'attributes'        => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"'
            ]
        ,   'show' => [
                'href'              => 'act=show'
            ,   'icon'              => 'show.svg'
            ]
        ]
    ]
,   'palettes' => [
        'default'           => '{common_legend},title,firstname,lastname,position,phone,fax,email;{source_legend},singleSRC;{page_legend},pages;{publish_legend},published'
    ]
,   'fields' => [
        'id' => [
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ]
    ,   'tstamp' => [
            'sql'           => "int(10) unsigned NOT NULL default 0"
        ]
    ,   'title' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'sorting'               => true
        ,   'eval'                  => ['maxlength'=>255, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(64) NOT NULL default ''"
        ]
    ,   'firstname' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'sorting'               => true
        ,   'eval'                  => ['maxlength'=>255, 'tl_class'=>'clr w50']
        ,   'sql'                   => "varchar(255) NOT NULL default ''"
        ]
    ,   'lastname' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'sorting'               => true
        ,   'eval'                  => ['mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(255) NOT NULL default ''"
        ]
    ,   'position' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'sorting'               => true
        ,   'eval'                  => ['maxlength'=>255, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(255) NOT NULL default ''"
        ]
    ,   'phone' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'eval'                  => ['maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(64) NOT NULL default ''"
        ]
    ,   'fax' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'eval'                  => ['maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(64) NOT NULL default ''"
        ]
    ,   'email' => [
            'exclude'               => true
        ,   'inputType'             => 'text'
        ,   'search'                => true
        ,   'sorting'               => true
        ,   'eval'                  => ['maxlength'=>255, 'rgxp'=>'email', 'decodeEntities'=>true, 'tl_class'=>'w50']
        ,   'sql'                   => "varchar(255) NOT NULL default ''"
        ]
    ,   'singleSRC' => [
            'exclude'               => true
        ,   'inputType'             => 'fileTree'
        ,   'eval'                  => ['fieldType'=>'radio', 'filesOnly'=>true, 'tl_class'=>'clr']
        ,   'sql'                   => "binary(16) NULL"
        ]
    ,   'pages' => [
            'exclude'               => true
        ,   'inputType'             => 'pageTree'
        ,   'foreignKey'            => 'tl_page.title'
        ,   'eval'                  => ['multiple'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr']
        ,   'sql'                   => "blob NULL"
        ,   'relation'              => ['type'=>'hasMany', 'load'=>'lazy']
        ]
    ,   'news' => [
            'exclude'               => true
        ,   'inputType'             => 'picker'
        ,   'foreignKey'            => 'tl_news.headline'
        ,   'eval'                  => ['multiple'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr']
        ,   'sql'                   => "blob NULL"
        ,   'relation'              => ['type'=>'hasMany', 'load'=>'lazy']
        ]
    ,   'events' => [
            'exclude'               => true
        ,   'inputType'             => 'picker'
        ,   'foreignKey'            => 'tl_calendar_events.title'
        ,   'eval'                  => ['multiple'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr']
        ,   'sql'                   => "blob NULL"
        ,   'relation'              => ['type'=>'hasMany', 'load'=>'lazy']
        ]
    ,   'published' => [
            'exclude'               => true
        ,   'inputType'             => 'checkbox'
        ,   'filter'                => true
        ,   'toggle'                => true
        ,   'eval'                  => ['doNotCopy'=>true]
        ,   'sql'                   => ['type'=>'boolean', 'default'=>false]
        ]
    ]
];