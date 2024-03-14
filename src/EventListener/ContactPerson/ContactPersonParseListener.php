<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\EventListener\ContactPerson;

use Contao\ContentModel;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\ModuleModel;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;


class ContactPersonParseListener {


    /**
     * @var Contao\CoreBundle\Image\Studio\Studio
     */
    private $imageStudio;


    public function __construct( Studio $imageStudio ) {

        $this->imageStudio = $imageStudio;
    }

    /**
     * Parses the given contact person
     *
     * @param numero2\ContactPersonsBundle\Event\ContactPersonParseEvent $event
     */
    public function __invoke( ContactPersonParseEvent $event ): void  {

        $contact = $event->getContactPerson();
        $model = $event->getModel();
        // $oPage = $event->getPageModel();

        // generate tel href
        if( strlen($contact['phone']) ) {
            $contact['phoneHref'] = self::genereateTelHref($contact['phone']);
        }
        if( strlen($contact['fax']) ) {
            $contact['faxHref'] = self::genereateTelHref($contact['fax'], 'fax');
        }

        // prepare image
        if( !empty($contact['singleSRC']) ) {

            $figureBuilder = $this->imageStudio->createFigureBuilder();
            $figureBuilder->fromUuid($contact['singleSRC']);

            if( $model instanceof ContentModel ) {
                if( $model->size ?? null ) {
                    $figureBuilder->setSize($model->size);
                }
            } else if( $model instanceof ModuleModel ) {
                if( $model->imgSize ?? null ) {
                    $figureBuilder->setSize($model->imgSize);
                }
            }

            $figure = $figureBuilder->buildIfResourceExists();

            if( $figure ) {
                $contact['singleSRCFigure'] = $figure->getLegacyTemplateData();
            }
        }

        $event->setContactPerson($contact);
    }


    /**
     * Generate a tel href from the given number and protocol
     *
     * @param string $number
     * @param string $protocol
     *
     * @return string
     */
    public static function genereateTelHref( string $number, string $protocol='tel' ): string {

        return $protocol.':'. preg_replace("|[^\+0-9]|", "", $number);
    }
}
