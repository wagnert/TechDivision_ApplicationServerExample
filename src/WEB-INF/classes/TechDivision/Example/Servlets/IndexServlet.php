<?php

/**
 * TechDivision\Example\Servlets\IndexServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Http
 * @author     Johann Zelger <jz@techdivision.com>
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Servlets;

use TechDivision\Context\BaseContext;
use TechDivision\Servlet\ServletConfig;
use TechDivision\Example\Controller\ControllerServlet;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle requests by subclasses action methods.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Controller
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class IndexServlet extends ControllerServlet
{

    /**
     * The array with the available route mappings.
     *
     * @var array
     */
    protected $mappings = array(
        '/index*'                => '\TechDivision\Example\Actions\IndexAction',
        '/upload*'               => '\TechDivision\Example\Actions\UploadAction',
        '/login*'                => '\TechDivision\Example\Actions\LoginAction',
        '/basicAuthentication*'  => '\TechDivision\Example\Actions\BasicAuthenticationAction',
        '/digestAuthentication*' => '\TechDivision\Example\Actions\DigestAuthenticationAction',
        '/webSocket*'            => '\TechDivision\Example\Actions\WebSocketAction',
        '/messageQueue*'         => '\TechDivision\Example\Actions\MessageQueueAction'
    );

    /**
     * The array with the initialized routes.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \TechDivision\Servlet\ServletConfig $config The configuration to initialize the servlet with
     *
     * @throws \TechDivision\Servlet\ServletException Is thrown if the configuration has errors
     * @return void
     */
    public function init(ServletConfig $config)
    {

        // call parent method
        parent::init($config);

        // initialize the routes
        foreach ($this->mappings as $route => $mapping) {
            $this->routes[$route] = new $mapping(new BaseContext());
        }
    }

    /**
     * Returns the available routes.
     *
     * @return array The array with the available routes
     */
    protected function getRoutes()
    {
        return $this->routes;
    }
}
