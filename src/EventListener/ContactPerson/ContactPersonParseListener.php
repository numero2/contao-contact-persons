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


namespace numero2\ContactPersonsBundle\EventListener\ContactPerson;

use Contao\ContentModel;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\FilesModel;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
use JeroenDesloovere\VCard\VCard;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class ContactPersonParseListener {


    /**
     * @var Contao\CoreBundle\Image\Studio\Studio
     */
    private Studio $imageStudio;

    /**
     * @var Contao\CoreBundle\Routing\ContentUrlGenerator
     */
    private ContentUrlGenerator $urlGenerator;


    public function __construct( Studio $imageStudio, ContentUrlGenerator $urlGenerator  ) {

        $this->imageStudio = $imageStudio;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Parses the given contact person
     *
     * @param numero2\ContactPersonsBundle\Event\ContactPersonParseEvent $event
     */
    public function __invoke( ContactPersonParseEvent $event ): void  {

        $contact = $event->getContactPerson();
        $model = $event->getModel();
        $oPage = $event->getPageModel();

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
                $contact['singleSRCFigure'] = $figure;
            }
        }

        $download = Input::get('vcfDownload');

        // check for file download and download matching vcf file
        if( $download && md5($contact['id']) == $download ) {

            $vcard = new VCard();

            // add vcf data
            $vcard->addName($contact['firstname'], $contact['lastname'], '', $contact['salutation']);
            $vcard->addJobtitle($contact['position']);
            $vcard->addEmail($contact['email']);
            $vcard->addPhoneNumber($contact['phone'], 'PREF;WORK');
            $vcard->addAddress(null, null, $contact['street'], $contact['city'], null, $contact['postal'], '');

            if( !empty($contact['singleSRC']) ) {

                $figureBuilder = $this->imageStudio->createFigureBuilder();
                $figureBuilder->fromUuid($contact['singleSRC']);
                $figureBuilder->setSize(['300', '300', 'crop']);
                $figure = $figureBuilder->buildIfResourceExists();
                $figure->getImage()->createIfDeferred();

                $vcard->addPhoto($figure->getImage()->getPicture()->getImg()['src']->getPath());
            }

            // trigger download of generated file
            $vcard->download();

            exit;
        }

        if( $contact['generate_vcf'] ) {

            if($oPage) {

                if( empty($contact['vcf_file']) ) {
                    $contact['vcf'] = $this->urlGenerator->generate($oPage, ["vcfDownload"=>md5($contact['id'])], UrlGeneratorInterface::ABSOLUTE_URL);
                } else {
                    $contact['vcf'] = FilesModel::findByUuid($contact['vcf_file'])->path;
                }
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
