<?php

/**
 * TechDivision\Example\Controller\Action
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
 * @subpackage Controller
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Controller;

use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

/**
 * This is the interface for all actions.
 *
 * Every action in a project has to implement this interface.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Controller
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface Action
{

    /**
     * All classes extending this class must implement the perform() method.
     *
     * This method implements the complete functionality of the action and have to return an initialized
     * ActionForward object that is necessary for further application flow controlled by the
     * ActionController.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function perform(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse);

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @return void
     */
    public function preDispatch();

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @return void
     */
    public function postDispatch();
}
