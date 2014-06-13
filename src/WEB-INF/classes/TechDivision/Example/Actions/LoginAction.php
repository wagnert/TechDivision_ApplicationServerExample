<?php

/**
 * TechDivision\Example\Actions\LoginAction
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

use TechDivision\Example\Utils\ContextKeys;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\Example\Exceptions\LoginException;

/**
 * Example servlet implementation that validates passed user credentials against
 * persistence container proxy and stores the user data in the session.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class LoginAction extends ExampleBaseAction
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     *
     * @var string
     */
    const LOGIN_TEMPLATE = 'static/templates/login.phtml';

    /**
     * Class name of the persistence container proxy that handles the data.
     *
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\UserProcessor';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Loads all sample data and attaches it to the servlet context ready to be rendered
     * by the template.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $viewData = $this->getProxy(LoginAction::PROXY_CLASS)->checkForDefaultCredentials();
        $this->setAttribute(ContextKeys::VIEW_DATA, $viewData);
        $servletResponse->appendBodyStream($this->processTemplate(LoginAction::LOGIN_TEMPLATE, $servletRequest, $servletResponse));
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function loginAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        try {

            // check if the necessary params has been specified and are valid
            if (($username = $servletRequest->getParameter('username')) === null) {
                throw new \Exception('Please enter a valid username');
            }

            // check if the necessary params has been specified and are valid
            if (($password = $servletRequest->getParameter('password')) === null) {
                throw new \Exception('Please enter a valid password');
            }

            // try to login
            $this->getProxy(LoginServlet::PROXY_CLASS)->login($username, $password);

            // if successfully then add the username to the session and redirect to the overview
            $session = $this->getLoginSession(true);
            $session->start();
            $session->putData('username', $username);

        } catch (LoginException $e) { // invalid login credentials
            $this->setAttribute('errorMessages', array("Username or Password invalid"));
        } catch (\Exception $e) { // if not add an error message
            $this->setAttribute('errorMessages', array($e->getMessage()));
        }

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Action that destroys the session and log the user out.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function logoutAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // destroy the session and reset the cookie
        $this->getLoginSession()->destroy('Explicit logout requested by: ' . $this->getUsername());

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }
}
