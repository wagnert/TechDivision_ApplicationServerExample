<?php

/**
 * TechDivision\Example\Servlets\ControllerServlet
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
use TechDivision\Servlet\Http\HttpSession;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\WebServer\Dictionaries\ServerVars;
use TechDivision\PersistenceContainerClient\ConnectionFactory;
use TechDivision\Example\Exceptions\LoginException;

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
class ControllerServlet extends HttpServlet
{

    /**
     * The default action if no valid action name was found in the path info.
     *
     * @var string
     */
    const DEFAULT_ACTION_NAME = 'index';

    /**
     * The default controller class suffix.
     *
     * @var string
     */
    const CONTROLLER_SUFFIX = 'Controller';

    /**
     * The default delimiter to extract the requested action name from the path info.
     *
     * @var string
     */
    const ACTION_DELIMITER = '/';

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

        // load the first part of the path info => that is the action name by default
        list ($requestedActionName, ) = explode(ControllerServlet::ACTION_DELIMITER, trim($servletRequest->getPathInfo(), ControllerServlet::ACTION_DELIMITER));

        // if the requested action has been found in the path info
        if ($requestedActionName == null) {
            $requestedActionName = ControllerServlet::DEFAULT_ACTION_NAME;
        }

        // if yes, concatenate it to create a valid action name
        $requestedActionName = $requestedActionName . ControllerServlet::ACTION_SUFFIX;

        $actionInstance = new $re

        // invoke the action itself
        $this->perform($servletRequest, $servletResponse);
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
}
