<?php

/**
 * TechDivision\Example\Servlets\IndexServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\Example\Servlets\AbstractServlet;
use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Utils\ContextKeys;

/**
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 */
class DigestAuthenticationServlet extends AbstractServlet implements Servlet {

    /**
     * The relative path, up from the webapp path, to the template to use.
     * @var string
     */
    const INDEX_TEMPLATE = 'static/templates/digestAuthentication.phtml';

    /**
     * Class name of the persistence container proxy that handles the data.
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\SampleProcessor';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     *
     * @param Request $req The request instance
     * @param Response $res The response instance
     * @return void
     */
    public function indexAction(Request $req, Response $res) {
        $res->setContent($this->processTemplate(self::INDEX_TEMPLATE, $req, $res));
    }

}