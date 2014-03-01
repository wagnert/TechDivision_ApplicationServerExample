<?php

/**
 * TechDivision\Example\Servlets\AbstractServlet
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

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Http\ServletRequest;
use TechDivision\ServletContainer\Http\ServletResponse;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle requests by subclasses action methods.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Servlets
 * @author     Johann Zelger <jz@techdivision.com>
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractServlet extends HttpServlet
{

    /**
     * Servlet context to transfer data between the servlet and the view.
     * 
     * @var array
     */
    protected $context = array();

    /**
     * The servlet request instance.
     * 
     * @var \TechDivision\ServletContainer\Http\ServletRequest
     */
    protected $servletRequest;

    /**
     * The servlet response instance.
     * 
     * @var \TechDivision\ServletContainer\Http\ServletResponse
     */
    protected $servletResponse;

    /**
     * The connection instance for the persistence container.
     * 
     * @var \TechDivision\PersistenceContainerClient\Context\ContextConnection
     */
    protected $connection;

    /**
     * The session instance for the persistence container connection.
     * 
     * @var \TechDivision\PersistenceContainerClient\Context\ContextSession
     */
    protected $session;

    /**
     * Initializes the connection to the persistence container.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->connection = Factory::createContextConnection('example');
        $this->session = $this->connection->createContextSession();
    }


    /**
     * Returns the base path to the web application.
     * 
     * @return string The base path
     */
    public function getWebappPath()
    {
        return $this->getServletConfig()->getWebappPath();
    }

    /**
     * Attaches the passed data under the also passed key in the servlet context.
     *
     * @param $key string The key to attach the data under
     * @param $value mixed The data to be attached
     * 
     * @return void
     */
    public function addAttribute($key, $value)
    {
        $this->context[$key] = $value;
    }

    /**
     * Returns the data for the passed key.
     *
     * @param string $key The key to return the data for
     * 
     * @return mixed The requested data
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
    }

    /**
     * Processes the template and returns the content.
     *
     * @param string $template Relative path to the template file
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return string The templates content
     */
    public function processTemplate($template, ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        // check if the template is available
        if (!file_exists($pathToTemplate = $this->getWebappPath() . DIRECTORY_SEPARATOR . $template)) {
            throw new \Exception("Requested template '$pathToTemplate' is not available");
        }
        // process the template
        ob_start();
        require $pathToTemplate;
        return ob_get_clean();
    }

    /**
     * Creates a new proxy for the passed session bean class name
     * and returns it.
     *
     * @param string $proxyClass The session bean class name to return the proxy for
     * 
     * @return mixed The proxy instance
     */
    public function getProxy($proxyClass)
    {
        $initialContext = $this->session->createInitialContext();
        return $initialContext->lookup($proxyClass);
    }

    /**
     * Implements Http GET method.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function doGet(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {

        // add request and response to session
        $this->setServletRequest($servletRequest);
        $this->setServletResponse($servletResponse);

        // start the session
        $this->getServletRequest()->getSession()->start();

        // load the request parameters
        $parameterMap = $servletRequest->getParameterMap();

        // evaluate the action method to be invoked
        $action = 'indexAction';
        if (array_key_exists('action', $parameterMap)) {
            $action = "{$parameterMap['action']}Action";
        }

        // invoke the action itself
        $this->$action($servletRequest, $servletResponse);
    }

    /**
     * Implements Http POST method.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function doPost(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $this->doGet($servletRequest, $servletResponse);
    }

    /**
     * Sets the servlet request instance.
     * 
     * @param \TechDivision\ServletContainer\Http\ServletRequest $servletRequest The request instance
     * 
     * @return void
     */
    public function setServletRequest(ServletRequest $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Sets the servlet response instance.
     * 
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return void
     */
    public function setServletResponse(ServletResponse $servletResponse)
    {
        $this->servletResponse = $servletResponse;
    }

    /**
     * Returns the servlet response instance.
     * 
     * @return \TechDivision\ServletContainer\Http\ServletRequest $servletRequest The request instance
     */
    public function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Returns the servlet request instance.
     * 
     * @return \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     */
    public function getServletResponse()
    {
        return $this->servletResponse;
    }

    /**
     * Returns base URL for the html base tag.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = '/';
        // if the application has NOT been called over a VHost configuration append application folder naem
        if (!$this->getServletConfig()->getApplication()->isVhostOf($this->getServletRequest()->getServerName())) {
            $baseUrl .= $this->getServletConfig()->getApplication()->getName() . '/';
        }
        return $baseUrl;
    }
}
