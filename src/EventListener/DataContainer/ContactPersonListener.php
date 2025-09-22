<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Model;
use Contao\StringUtil;
use numero2\ContactPersonsBundle\ContactPersonRelPageModel;
use numero2\ContactPersonsBundle\ContactPersonModel;
use Contao\CalendarEventsModel;
use Contao\NewsModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Exception;


class ContactPersonListener {


    /**
     * Load pages from relation table
     *
     * @param mixed $value
     * @param Contao\DataContainer $dc
     *
     */
    #[AsCallback('tl_contact_person', target: 'fields.pages.load')]
    #[AsCallback('tl_contact_person', target: 'fields.news.load')]
    #[AsCallback('tl_contact_person', target: 'fields.events.load"')]
    public function loadPagesFromRelationTable( $value, DataContainer $dc ) {

        if( !$dc->id  || !strlen($dc->field) ) {
            return $value;
        }

        $pid = $dc->id;
        $table = null;
        if( !empty($GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['foreignKey']) ) {
            $table = explode('.', $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['foreignKey'], 2)[0];
        }

        $strClass = "";

        try {
            $strClass = Model::getClassFromTable($table);
        } catch (Exception $e) {}

        if( empty($strClass) || !class_exists($strClass) ) {
            unset($GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]);
            return $value;
        }

        $relations = ContactPersonRelPageModel::findBy(['pid=? AND page_table=?'], [$pid, $table]);

        $value = [];

        if( $relations ) {
            foreach( $relations as $relation ) {
                $value[] = $relation->page_id;
            }
        }

        return $value;
    }


    /**
     * Save pages in relation table
     *
     * @param mixed $value
     * @param Contao\DataContainer $dc
     *
     */
    #[AsCallback('tl_contact_person', target: 'fields.pages.save')]
    #[AsCallback('tl_contact_person', target: 'fields.news.save')]
    #[AsCallback('tl_contact_person', target: 'fields.events.save')]
    public function savePagesInRelationTable( $value, DataContainer $dc ) {

        if( !$dc->id  || !strlen($dc->field) ) {
            return $value;
        }

        $pid = $dc->id;
        $table = null;
        if( !empty($GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['foreignKey']) ) {
            $table = explode('.', $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['foreignKey'], 2)[0];
        }

        if( !is_array($value) ) {
            $value = StringUtil::deserialize($value, true);
        }

        $relations = ContactPersonRelPageModel::findBy(['pid=? AND page_table=?'], [$pid, $table]);

        $value = array_values($value);

        $i = 0;

        if( $relations ) {
            foreach( $relations as $relation ) {

                if( empty($value[$i]) ) {
                    $relation->delete();

                } else if( $relation->page_id !== $value[$i] ) {

                    $relation->tstamp = time();
                    $relation->page_id = $value[$i];

                    $relation->save();
                }

                $i += 1;
            }
        }

        for( $c=count($value); $i < $c; $i++ ) {

            $relation = new ContactPersonRelPageModel();
            $relation->pid = $pid;
            $relation->tstamp = time();
            $relation->page_table = $table;
            $relation->tstamp = time();
            $relation->page_id = $value[$i];

            $relation->save();
        }

        return $value;
    }

    /**
     * Add missing palette fields for events and news
     *
     * @param Contao\DataContainer $dc
     *
     */
    #[AsCallback('tl_contact_person', target: 'config.onload')]
    public function addMissingPaletteFields( DataContainer $dc ): void {

        $active = $dc->getActiveRecord();

        $t = ContactPersonModel::getTable();

        if (class_exists(NewsModel::class)) {
            PaletteManipulator::create()
            ->addField('news', 'page_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', $t);
            ;
        }

        if (class_exists(CalendarEventsModel::class)) {
            PaletteManipulator::create()
                ->addField('events', 'page_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', $t);
            ;
        }
    }
}
