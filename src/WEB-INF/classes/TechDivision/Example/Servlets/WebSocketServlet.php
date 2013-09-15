<?php

/**
 * TechDivision\Example\Servlets\WebSocketServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\Example\Servlets\AbstractServlet;

/**
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class WebSocketServlet extends AbstractServlet implements Servlet 
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     * @var string
     */
    const WEBSOCKET_TEMPLATE = 'static/templates/websocket.phtml';
    
    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Loads all sample data and attaches it to the servlet context ready to be rendered
     * by the template.
     *
     * @param Request $req The request instance
     * @param Response $res The response instance
     * @return void
     */
    public function indexAction(Request $req, Response $res)
    {
        $res->setContent($this->processTemplate(self::WEBSOCKET_TEMPLATE, $req, $res));
    }
}