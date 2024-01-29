<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use numero2\ContactPersonsBundle\ContactPersonsBundle;


class Plugin implements BundlePluginInterface {


    /**
     * {@inheritdoc}
     */
    public function getBundles( ParserInterface $parser ): array {

        return [
            BundleConfig::create(ContactPersonsBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class
                ,   'notification_center'
                ])
        ];
    }
}
