<?php

/**
 * TechDivision\Example\Utils\ContextKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Utils;

/**
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ContextKeys {

    /**
     * Private to constructor to avoid instancing this class.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * The key for a collection with entities.
     *
     * @return string
     */
    const OVERVIEW_DATA = 'overview.data';

    /**
     * The key for an entity.
     * @return string
     */
    const VIEW_DATA = 'view.data';
}