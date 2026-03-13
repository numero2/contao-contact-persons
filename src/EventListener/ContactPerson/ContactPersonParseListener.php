<?php

/**
 * Contact persons bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @author    Christopher Brandt <christopher.brandt@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2026, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\ContactPersonsBundle\EventListener\ContactPerson;

use Contao\ContentModel;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\FilesModel;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use JeroenDesloovere\VCard\VCard;
use numero2\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @var Symfony\Component\HttpFoundation\RequestStack
     */
    private RequestStack $requestStack;


    public function __construct( Studio $imageStudio, ContentUrlGenerator $urlGenerator, RequestStack $requestStack ) {

        $this->imageStudio = $imageStudio;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }


    /**
     * Parses the given contact person
     *
     * @param numero2\ContactPersonsBundle\Event\ContactPersonParseEvent $event
     */
    public function __invoke( ContactPersonParseEvent $event ): void  {

        $contact = $event->getContactPerson();
        $model = $event->getModel();
        $page = $event->getPageModel();

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

            if( $page ) {

                if( empty($contact['vcf_file']) ) {

                    if( $page->requireItem ) {

                        $request = $this->requestStack->getMainRequest();
                        $contact['vcf'] = Request::create($request->getUri(), 'GET', ['vcfDownload'=>md5($contact['id'])])->getUri();

                    } else {

                        $contact['vcf'] = $this->urlGenerator->generate($page, ['vcfDownload'=>md5($contact['id'])], UrlGeneratorInterface::ABSOLUTE_URL);
                    }

                } else {
                    $contact['vcf'] = FilesModel::findByUuid($contact['vcf_file'])->path;
                }
            }
        }

        // generate jumpTo href
        if( $contact['jumpTo'] ) {

            $target = PageModel::findById($contact['jumpTo']);
            $href = $this->urlGenerator->generate($target, [], UrlGeneratorInterface::ABSOLUTE_URL);

            $contact['href'] = $href;
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
