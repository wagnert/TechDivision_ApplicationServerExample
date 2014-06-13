<?php

/**
 * TechDivision\Example\Controller\BaseAction
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

use TechDivision\Lang\Object;
use TechDivision\Context\Context;

/**
 * This class is the abstract base class for all Actions.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Controller
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class BaseAction extends Object implements Action
{

    /**
     * The context for the actual request.
     *
     * @var \TechDivision\Context\Context
     */
    protected $context = null;

    /**
     * Initializes the action with the context for the
     * actual request.
     *
     * @param \TechDivision\Context\Context $context The context for the actual request
     *
     * @return void
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @return void
     * @see \TechDivision\Example\Controller\Action::preDispatch()
     */
    public function preDispatch()
    {
        return;
    }

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @return void
     * @see \TechDivision\Example\Controller\Action::preDispatch()
     */
    public function postDispatch()
    {
        return;
    }

    /**
     * Returns the context for the actual request.
     *
     *  @return \TechDivision\Context\Context The context for the actual request
     */
    public function getContext()
    {
        return $this->context;
    }
}
