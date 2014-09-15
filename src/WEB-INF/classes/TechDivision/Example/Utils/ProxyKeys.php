<?php

/**
 * TechDivision\Example\Utils\ProxyKeys
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
 * @subpackage Utils
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Utils;

/**
 * Context keys that are used to store data in a application context.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Utils
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ProxyKeys
{

    /**
     * Private to constructor to avoid instancing this class.
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * The naming directory key for the 'TechDivision\Example\Services\SampleProcessor' session bean.
     *
     * @return string
     */
    const SAMPLE_PROCESSOR = 'php:app/SampleProcessor/local';

    /**
     * The naming directory key for the 'TechDivision\Example\Services\UserProcessor' session bean.
     *
     * @return string
     */
    const USER_PROCESSOR = 'php:app/UserProcessor/local';
}
