<?php

/**
 * TechDivision\Example\Controller\DispatchAction
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
 * This class implements the functionality to invoke a method on its subclass specified
 * by the HTTPServletRequest path info.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Controller
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class DispatchAction extends BaseAction
{

    /**
     * The default action method suffix.
     *
     * @var string
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * Holds the name of the default method to invoke if the paramter with the method name to invoke is not specified.
     *
     * @var string
     */
    const DEFAULT_METHOD_NAME = 'index';

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

        // load the first part of the path info => that is the action name by default
        list (, $requestedMethodName) = explode(ControllerServlet::ACTION_DELIMITER, trim($servletRequest->getPathInfo(), ControllerServlet::ACTION_DELIMITER));

        // try to set the default method, if one is specified in the path info
        if ($requestedMethodName == null) {
            $requestedMethodName = $this->getDefaultMethod();
        }

        // if yes, concatenate it to create a valid action name
        $requestedActionMethod = $requestedMethodName . DispatchAction::ACTION_SUFFIX;

        // check if the requested action method is a class method
        if (in_array($requestedActionMethod, get_class_methods($this))) {
            $actionMethod = $requestedActionMethod;
        }

        // initialize a new reflection object
        $reflectionObject = new ReflectionObject($this);

        // check if the specified method is implemented in the sublass
        if ($reflectionObject->hasMethod($actionMethod) === false) {
            throw new MethodNotFoundException(sprintf('Specified method %s not implemented by class %s', $actionMethod, get_class($this)));
        }

        // get the reflection method
        $reflectionMethod = $reflectionObject->getMethod($actionMethod);

        // invoke the requested action method
        $reflectionMethod->invoke($servletRequest, $servletResponse);
    }

    /**
     * This method returns the default method name we'll invoke if the path info doesn not contain
     * the method name, that'll be the second element, when we explode the path info with a slash.
     *
     * @return string The default action method name that has to be invoked
     */
    protected function getDefaultMethod()
    {
        return DispatchAction::DEFAULT_METHOD_NAME;
    }
}
