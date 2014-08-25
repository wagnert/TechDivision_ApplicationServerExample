<?php

/**
 * TechDivision\Example\Actions\ExampleBaseAction
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
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Actions;

use AppserverIo\Routlt\DispatchAction;
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpSession;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\WebServer\Dictionaries\ServerVars;
use TechDivision\PersistenceContainerClient\ConnectionFactory;
use TechDivision\Example\Exceptions\LoginException;
use TechDivision\Example\Utils\SessionKeys;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle requests by subclasses action methods.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class ExampleBaseAction extends DispatchAction
{

    /**
     * The applications base URL.
     *
     * @var string
     */
    const BASE_URL = '/';

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
     * This method implements the functionality to invoke a method implemented in its subclass.
     *
     * The method that should be invoked has to be specified by a HTTPServletRequest parameter
     * which name is specified in the configuration file as parameter for the ActionMapping.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function perform(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        $this->setServletRequest($servletRequest);
        $this->setServletResponse($servletResponse);

        parent::perform($servletRequest, $servletResponse);
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
     * @return \TechDivision\Servlet\Http\HttpServletRequest The request instance
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
     * Attaches the passed data under the also passed key in the servlet context.
     *
     * @param string $key   The key to attach the data under
     * @param mixed  $value The data to be attached
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->context->setAttribute($key, $value);
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
        return $this->context->getAttribute($key);
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

        // load the path to the web application
        $webappPath = $servletRequest->getContext()->getWebappPath();

        // check if the template is available
        if (!file_exists($pathToTemplate = $webappPath . DIRECTORY_SEPARATOR . $template)) {
            throw new \Exception(sprintf('Requested template \'%2\' is not available', $pathToTemplate));
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

        // load the application name
        $applicationName = $this->getServletRequest()->getContext()->getName();

        // initialize the connection and the session
        $connection = ConnectionFactory::createContextConnection($applicationName);
        $session = $connection->createContextSession();

        // check if we've a session ID
        if ($this->isLoggedIn()) {
            $session->setSessionId($this->getLoginSession()->getId());
        }

        // create an return the proxy instance
        return $session->createInitialContext()->lookup($proxyClass);
    }

    /**
     * Returns the session with the passed session name.
     *
     * @param boolean $create TRUE if a session has to be created if we can't find any
     *
     * @return \TechDivision\Servlet\Http\HttpSession|null The requested session instance
     * @throws \Exception Is thrown if we can't find a request instance
     */
    public function getLoginSession($create = false)
    {

        // try to load the servlet request
        $servletRequest = $this->getServletRequest();
        if ($servletRequest == null) {
            throw new \Exception('Can\'t find necessary servlet request instance');
        }

        // return the session
        return $servletRequest->getSession($create);
    }

    /**
     * Returns TRUE if a user has been logged in, else FALSE.
     *
     * @return boolean TRUE if a user has been logged into the sytem
     */
    public function isLoggedIn()
    {

        // try to load the session
        $session = $this->getLoginSession();

        // if we can't find a session, something went wrong
        if ($session == null) {
            return false;
        }

        // if we can't find a username, also something went wrong
        if ($session->hasKey(SessionKeys::USERNAME) === false) {
            return false;
        }

        // return the name of the registered user
        return true;
    }

    /**
     * Returns the link to logout the actual user.
     *
     * @return string The link to logout the user actually logged in
     */
    public function getLogoutLink()
    {
        return 'index.do/login/logout';
    }

    /**
     * Returns the name of the user currently logged into the system.
     *
     * @return string Name of the user logged into the system
     * @throws \TechDivision\Example\Exceptions\LoginException Is thrown if we can't find a session or a user logged in
     */
    public function getUsername()
    {

        // try to load the session
        $session = $this->getLoginSession();

        // if we can't find a session, something went wrong
        if ($session == null) {
            throw new LoginException(sprintf('Can\'t find session %s', $this->getServletRequest()->getRequestedSessionName()));
        }

        // if we can't find a username, also something went wrong
        if ($session->hasKey(SessionKeys::USERNAME) === false) {
            throw new LoginException('Session has no user registered');
        }

        // return the name of the registered user
        return $session->getData(SessionKeys::USERNAME);
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
            return ExampleBaseAction::BASE_URL;
        }

        // if not, prepend it with the context path
        return $this->getServletRequest()->getContextPath() . ExampleBaseAction::BASE_URL;
    }
}
