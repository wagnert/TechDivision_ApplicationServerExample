<?php

/**
 * TechDivision\Example\Servlets\HelloWorldServlet
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
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Servlets;

use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

/**
 * Demo servlet handling GET requests.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class HelloWorldServlet extends HttpServlet
{

    /**
     * The user processor instance.
     *
     * @var \TechDivision\Example\Services\SampleProcessor
     * @EnterpriseBean(name="SampleProcessor")
     */
    protected $sampleProcessor;

    /**
     * The user processor instance (a SFB instance).
     *
     * @var \TechDivision\Example\Services\UserProcessor
     * @EnterpriseBean(name="UserProcessor")
     */
    protected $userProcessor;

    /**
     * Handles a HTTP GET request.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @throws \TechDivision\Servlet\ServletException Is thrown if the request method is not implemented
     * @see \TechDivision\Servlet\Http\HttpServlet::doGet()
     */
    public function doGet(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // check if we've a user logged into the system
        if ($loggedInUser = $this->userProcessor->getUserViewDataOfLoggedIn()) {
            $servletRequest->getContext()->getInitialContext()->getSystemLogger()->info(
                sprintf("Found user logged in: %s", $loggedInUser->getUsername())
            );
        }

        // log the number of samples found in the databse
        $servletRequest->getContext()->getInitialContext()->getSystemLogger()->info(
            sprintf("Found %d samples", sizeof($this->sampleProcessor->findAll()))
        );

        // append the Hello World! to the body stream
        $servletResponse->appendBodyStream('Hello World!');
    }
}
