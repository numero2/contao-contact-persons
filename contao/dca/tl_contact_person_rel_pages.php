<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\DC_Table;
use numero2\ContactPersonsBundle\ContactPersonModel;
use numero2\ContactPersonsBundle\ContactPersonRelPageModel;


$GLOBALS['TL_DCA'][ContactPersonRelPageModel::getTable()] = [

    'config' => [
        'dataContainer'             => DC_Table::class
    ,   'ptable'                    => ContactPersonModel::getTable()
    ,   'sql' => [
            'keys' => [
                'id' => 'primary'
            ,   'pid' => 'index'
            ,   'page_table,page_id' => 'index'
            ]
        ]
    ]
,   'fields' => [
        'id' => [
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ]
    ,   'pid' => [
            'sql'           => "int(10) unsigned NOT NULL default 0"
        ]
    ,   'tstamp' => [
            'sql'           => "int(10) unsigned NOT NULL default 0"
        ]
    ,   'page_table' => [
            'sql'           => "varchar(64) NOT NULL default ''"
        ]
    ,   'page_id' => [
            'sql'           => "int(10) unsigned NOT NULL default 0"
        ]
    ]
];