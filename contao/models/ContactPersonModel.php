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


class ContactPersonModel extends Model {


    /**
     * @var string
     */
    protected static $strTable = 'tl_contact_person';


    /**
     * Find one published contact by their IDs
     *
     * @param string|int $ids
     * @param array $options
     *
     * @return Conato\Model|null The model collection or null if there are no contact
     */
    public static function findPublishedById( $id, array $options=[] ) {

        if( empty($id) ) {
            return null;
        }

        $t = static::$strTable;

        $options = array_merge([
            'column' => ["$t.id=?"],
            'value'  => [$id],
            'return' => 'Model',
        ], $options);

        if( !static::isPreviewMode($options) ) {
            $options['column'][] = "$t.published='1'";
        }

        return static::find($options);
    }


    /**
     * Find multiple published contact by their IDs
     *
     * @param array $ids
     * @param array $options
     *
     * @return Conato\Collection|null The model collection or null if there are no contact
     */
    public static function findMultiplePublishedById( array $ids, array $options=[] ) {

        if( empty($ids) || !\is_array($ids) ) {
            return null;
        }

        $t = static::$strTable;

        $options = array_merge([
            'column' => ["$t.id IN(" . implode(',', array_map('\intval', $ids)) . ")"],
            'value'  => [],
            'return' => 'Collection',
        ], $options);

        if( !static::isPreviewMode($options) ) {
            $options['column'][] = "$t.published='1'";
        }

        return static::find($options);
    }
}