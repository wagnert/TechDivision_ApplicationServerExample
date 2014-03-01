<?php

/**
 * TechDivision\Example\Servlets\LoginServlet
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
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Http\ServletRequest;
use TechDivision\ServletContainer\Http\ServletResponse;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\Example\Servlets\AbstractServlet;
use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Utils\ContextKeys;
use TechDivision\Example\Exceptions\LoginException;

/**
 * Example servlet implementation that validates passed user credentials against
 * persistence container proxy and stores the user data in the session.
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
class LoginServlet extends AbstractServlet
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
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $servletResponse->setContent($this->processTemplate(self::LOGIN_TEMPLATE, $servletRequest, $servletResponse));
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function loginAction(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {

        try {

            // load the params with the entity data
            $parameterMap = $servletRequest->getParameterMap();
    
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
            
            // try to login
            $this->getProxy(self::PROXY_CLASS)->login($username, $password);

            // if successfully then add the username to the session and redirect to the overview
            $servletRequest->getSession()->putData('username', $username);
            $servletResponse->addHeader('Location', $this->getBaseUrl() . 'index/index');
            $servletResponse->addHeader("status", 'HTTP/1.1 301 OK');

        } catch (LoginException $e) { // invalid login credentials
            $this->addAttribute('errorMessages', array("Username or Password invalid"));
        } catch (\Exception $e) { // if not add an error message
            $this->addAttribute('errorMessages', array($e->getMessage()));
        }

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }
}