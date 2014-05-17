<?php

/**
 * TechDivision\Example\Services\UserProcessor
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
 * @subpackage Services
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Services;

use TechDivision\Example\Exceptions\LoginException;

/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Services
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 *
 * @Stateless
 */
class UserProcessor extends AbstractProcessor
{

    /**
     * The default username.
     *
     * @var string
     */
    const DEFAULT_USERNAME = 'appserver';

    /**
     * Validates the passed username agains the password.
     *
     * @param string $username The username to login with
     * @param string $password The password that should match the username
     *
     * @return void
     * @throws TechDivision\Example\Exceptions\LoginException Is thrown if the user with the passed username doesn't exist or match the password
     */
    public function login($username, $password)
    {

        // load the entity manager and the user repository
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\User');

        // try to load the user
        $user = $repository->findOneBy(array('username' => $username));
        if ($user == null) {
            throw new LoginException('Username or Password doesn\'t match');
        }

        // try to match the passwords
        if ($user->getPassword() !== md5($password)) {
            throw new LoginException('Username or Password doesn\'t match');
        }
    }

    /**
     * Checks if a default user exists, if not it creates one and returns the entity.
     *
     * @return TechDivision\Example\Entities\User The default user instance
     */
    public function checkForDefaultCredentials()
    {
        // load the entity manager and the user repository
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\User');

        // try to load the default credentials
        $defaultUser = $repository->findOneBy(array('username' => UserProcessor::DEFAULT_USERNAME));
        if ($defaultUser == null) {
            $defaultUser = $this->createDefaultCredentials();
        }

        // return the default credentials
        return $defaultUser;
    }

    /**
     * Creates the default credentials to login.
     *
     * @return TechDivision\Example\Entities\User The default user instance
     */
    public function createDefaultCredentials()
    {

        try {

            // load the entity manager
            $entityManager = $this->getEntityManager();

            // set user data and save it
            $user = $this->getApplication()->newInstance('\TechDivision\Example\Entities\User');
            $user->setEmail('info@appserver.io');
            $user->setUsername(UserProcessor::DEFAULT_USERNAME);
            $user->setUserLocale('en_US');
            $user->setPassword(md5('appserver.i0'));
            $user->setEnabled(true);
            $user->setRate(1000);
            $user->setContractedHours(160);
            $user->setLdapSynced(false);
            $user->setSyncedAt(time());

            // persist the user
            $entityManager->persist($user);

            // flush the entity manager
            $entityManager->flush();

            // create the created user instance
            return $user;

        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }
}
