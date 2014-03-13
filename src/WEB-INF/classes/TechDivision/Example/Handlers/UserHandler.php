<?php

/**
 * TechDivision\Example\Handlers\UserHandler
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

namespace TechDivision\Example\Handlers;

/**
 * This is a web socket handler that handles requests
 * related with users.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class UserHandler extends BaseHandler
{

    /**
     * Class name of the persistence container proxy that handles the data.
     * 
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\UserProcessor';

    /**
     * Returns all user entities.
     *
     * @return array The array with the user entities
     */
    public function overviewAction()
    {
        return $this->getProxy(self::PROXY_CLASS)->findAll();
    }
}
