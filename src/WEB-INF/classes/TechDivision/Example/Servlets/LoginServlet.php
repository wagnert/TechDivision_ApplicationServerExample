<?php

/**
 * TechDivision\Example\Servlets\LoginServlet
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
 * @author      Tim Wagner <tw@techdivision.com>
 */
class LoginServlet extends AbstractServlet implements Servlet {

    /**
     * The relative path, up from the webapp path, to the template to use.
     * @var string
     */
    const LOGIN_TEMPLATE = 'templates/login.phtml';

    /**
     * Class name of the persistence container proxy that handles the data.
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\SampleProcessor';

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
    public function indexAction(Request $req, Response $res) {
        $res->setContent($this->processTemplate(self::LOGIN_TEMPLATE, $req, $res));
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     * @param Request $req The request instance
     * @param Response $res The response instance
     * @return void
     * @see IndexServlet::indexAction()
     */
    public function loginAction(Request $req, Response $res) {

        // load the params with the entity data
        $parameterMap = $req->getParameterMap();

        // check if the necessary params has been specified and are valid
        if (!array_key_exists('username', $parameterMap)) {
            throw new \Exception();
        } else {
            $username = filter_var($parameterMap['username'], FILTER_SANITIZE_STRING);
        }

        // check if the necessary params has been specified and are valid
        if (!array_key_exists('password', $parameterMap)) {
            throw new \Exception();
        } else {
            $password = filter_var($parameterMap['password'], FILTER_SANITIZE_STRING);
        }

        if ($username == 'admin' && $password == 'password') {

            $res->addHeader('Location', 'index/index');
            $req->getSession()->putData('username', $username);


        } else {
            $this->addAttribute('errorMessages', array('Invalid username or password'));
        }

        // reload all entities and render the dialog
        $this->indexAction($req, $res);
    }
}