<?php

/**
 * TechDivision\Example\Utils\RequestKeys
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
 * Request keys that are used to store data in a request context.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Utils
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class RequestKeys
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
     * The key for a 'action'.
     *
     * @return string
     */
    const ACTION = 'action';

    /**
     * The key for a 'sampleId'.
     *
     * @return string
     */
    const SAMPLE_ID = 'sampleId';

    /**
     * The key for a 'name'.
     *
     * @return string
     */
    const NAME = 'name';

    /**
     * The key for a 'username'.
     *
     * @return string
     */
    const USERNAME = 'username';

    /**
     * The key for a 'password'.
     *
     * @return string
     */
    const PASSWORD = 'password';

    /**
     * The key for a 'filename'.
     *
     * @return string
     */
    const FILENAME = 'filename';

    /**
     * The key for a 'fileToUpload'.
     *
     * @return string
     */
    const FILE_TO_UPLOAD = 'fileToUpload';

    /**
     * The key for a 'userId'.
     *
     * @return string
     */
    const USER_ID = 'userId';

    /**
     * The key for a 'ldapSynced'.
     *
     * @return string
     */
    const LDAP_SYNCED = 'ldapSynced';

    /**
     * The key for a 'enabled'.
     *
     * @return string
     */
    const ENABLED = 'enabled';

    /**
     * The key for a 'syncedAt'.
     *
     * @return string
     */
    const SYNCED_AT = 'syncedAt';

    /**
     * The key for a 'contractedHours'.
     *
     * @return string
     */
    const CONTRACTED_HOURS = 'contractedHours';

    /**
     * The key for a 'rate'.
     *
     * @return string
     */
    const RATE = 'rate';

    /**
     * The key for a 'email'.
     *
     * @return string
     */
    const EMAIL = 'email';

    /**
     * The key for a 'userLocale'.
     *
     * @return string
     */
    const USER_LOCALE = 'userLocale';
}
