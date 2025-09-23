<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @author    Christopher Brandt <christopher.brandt@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


class ContactPersonsBundle extends Bundle {


    /**
     * {@inheritdoc}
     */
    public function getPath(): string {

        return \dirname(__DIR__);
    }
}