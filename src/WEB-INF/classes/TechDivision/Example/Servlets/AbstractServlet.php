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

use TechDivision\Servlet\ServletConfig;
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\WebServer\Dictionaries\ServerVars;
use TechDivision\PersistenceContainerClient\ConnectionFactory;

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
     * The default action if no valid action name was found in the path info.
     *
     * @var string
     */
    const DEFAULT_ACTION_NAME = 'index';

    /**
     * The default action method suffix.
     *
     * @var string
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * The default delimiter to extract the requested action name from the path info.
     *
     * @var string
     */
    const ACTION_DELIMITER = '/';

    /**
     * The applications base URL.
     *
     * @var string
     */
    const BASE_URL = '/';

    /**
     * Servlet context to transfer data between the servlet and the view.
     *
     * @var array
     */
    protected $context = array();

    /**
     * The servlet request instance.
     *
     * @var \TechDivision\Servlet\Http\HttpServletRequest
     */
    protected $servletRequest;

    /**
     * The servlet response instance.
     *
     * @var \TechDivision\Servlet\Http\HttpServletResponse
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
     * @param \TechDivision\Servlet\ServletConfig $config The servlet configuration
     *
     * @return void
     */
    public function init(ServletConfig $config)
    {
        // call parent method to set configuration
        parent::init($config);

        // initialize the persistence container proxy
        $this->connection = ConnectionFactory::createContextConnection('example');
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
     * @param string $key   The key to attach the data under
     * @param mixed  $value The data to be attached
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
     * @param string                                         $template        Relative path to the template file
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return string The templates content
     */
    public function processTemplate($template, HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
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
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function doGet(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // add request and response to session
        $this->setServletRequest($servletRequest);
        $this->setServletResponse($servletResponse);

        // initialize the session (create a new one if necessary)
        $session = $this->getServletRequest()->getSession(true);
        $session->setSessionCookieHttpOnly(true);
        $session->start();

        // set the session ID on the persistence container proxy
        $this->session->setSessionId($session->getId());

        // create the default action => indexAction
        $actionMethod = AbstractServlet::DEFAULT_ACTION_NAME . AbstractServlet::ACTION_SUFFIX;

        // load the first part of the path info => that is the action name by default
        list ($requestedActionName, ) = explode(AbstractServlet::ACTION_DELIMITER, trim($servletRequest->getPathInfo(), AbstractServlet::ACTION_DELIMITER));

        // if the requested action has been found in the path info
        if (empty($requestedActionName) === false) {

            // if yes, concatenate it to create a valid action name
            $requestedActionMethod = $requestedActionName . AbstractServlet::ACTION_SUFFIX;

            // check if the requested action method is a class method
            if (in_array($requestedActionMethod, get_class_methods($this))) {
                $actionMethod = $requestedActionMethod;
            }
        }

        // invoke the action itself
        $this->$actionMethod($servletRequest, $servletResponse);
    }

    /**
     * Implements Http POST method.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function doPost(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $this->doGet($servletRequest, $servletResponse);
    }

    /**
     * Sets the servlet request instance.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest $servletRequest The request instance
     *
     * @return void
     */
    public function setServletRequest(HttpServletRequest $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Sets the servlet response instance.
     *
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The request instance
     *
     * @return void
     */
    public function setServletResponse(HttpServletResponse $servletResponse)
    {
        $this->servletResponse = $servletResponse;
    }

    /**
     * Returns the servlet response instance.
     *
     * @return \TechDivision\Servlet\Http\ServletRequest The request instance
     */
    public function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Returns the servlet request instance.
     *
     * @return \TechDivision\Servlet\Http\HttpServletResponse The response instance
     */
    public function getServletResponse()
    {
        return $this->servletResponse;
    }

    /**
     * Returns base URL for the html base tag.
     *
     * @return string The base URL depending on the vhost
     */
    public function getBaseUrl()
    {

        // if we ARE in a virtual host, return the base URL
        if ($this->getServletRequest()->getContext()->isVhostOf($this->getServletRequest()->getServerName())) {
            return AbstractServlet::BASE_URL;
        }

        // if not, prepend it with the context path
        return $this->getServletRequest()->getContextPath() . AbstractServlet::BASE_URL;
    }
}
