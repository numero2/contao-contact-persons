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

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;


class DefaultTemplateOptionsListener {


    /**
     * @var Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $templatePrefix;

    /**
     * @var string
     */
    private $defaultTemplate;

    /**
     * @var Contao\CoreBundle\Framework\Adapter<Controller>
     */
    private $controller;


   public function __construct( ContaoFramework $framework, RequestStack $requestStack, string $templatePrefix, string $defaultTemplate=null ) {

       $controller = $framework->getAdapter(Controller::class);

       $this->controller = $controller;
       $this->requestStack = $requestStack;
       $this->templatePrefix = $templatePrefix;
       $this->defaultTemplate = $defaultTemplate;
   }


    public function __invoke( DataContainer $dc ): array {

        if( $this->isOverrideAll() ) {
            // Add a blank option that allows us to reset all custom templates to the default one
            return array_merge(['' => '-'], $this->controller->getTemplateGroup($this->templatePrefix));
        }

        $defaultTemplate = '';

        if( $this->defaultTemplate !== null ) {

            $defaultTemplate = $this->defaultTemplate;

        } else if( isset($dc->activeRecord->type) ) {

            $defaultTemplate = $this->templatePrefix.$dc->activeRecord->type;
        }

        $options = $this->controller->getTemplateGroup($this->templatePrefix, [], $defaultTemplate);

        if( $this->defaultTemplate !== null && strlen($this->defaultTemplate) && array_key_exists($this->defaultTemplate, $options) ) {
            unset($options[$this->defaultTemplate]);
        }

        return $options;
    }


    private function isOverrideAll(): bool {

        $request = $this->requestStack->getCurrentRequest();

        if( null === $request || !$request->query->has('act') ) {
            return false;
        }

        return 'overrideAll' === $request->query->get('act');
    }
}