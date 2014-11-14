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
use TechDivision\Example\Exceptions\UserNotFoundException;

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
 * @Stateful
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
     * The user, logged into the system.
     *
     * @var \TechDivision\Example\Entities\User
     */
    protected $user;

    /**
     * Example method that should be invoked after constructor.
     *
     * @return void
     * @PreDestroy
     */
    public function destroy()
    {
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('%s has successfully been invoked by @PreDestroy annotation', __METHOD__)
        );
    }

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

        // store the user in the session
        $this->user = $user;
    }

    /**
     * Returns the user actually logged into the system.
     *
     * @return \TechDivision\Example\Entities\User|null The user instance
     */
    public function getUserViewDataOfLoggedIn()
    {
        return $this->user;
    }

    /**
     * Returns the data of the user that has been logged into the system.
     *
     * This method is an example implementation on how you can use a stateful
     * session bean to temporary store session data.
     *
     * @param string $username The username of the user to return the data for
     *
     * @return \TechDivision\Example\Entities\User The user logged into the system
     * @throws \TechDivision\Example\Exceptions\FoundInvalidUserException Is thrown if no user has been logged into the system or the username doesn't match
     * @see \TechDivision\Example\Services\UserProcessor::login()
     */
    public function getUserViewData($username)
    {

        // if we already have a user, compare the username
        if ($this->user != null && $this->user->getUsername() != $username) {
            throw new FoundInvalidUserException(sprintf('Username of user logged into the system doesn\'t match %s', $username));
        }

        // if no user has been loaded, try to load the user
        if ($this->user == null) {

            // load the entity manager and the user repository
            $entityManager = $this->getEntityManager();
            $repository = $entityManager->getRepository('TechDivision\Example\Entities\User');

            // reload the user from the repository
            $this->user = $repository->findOneBy(array('username' => $username));

            // log a message that the data has been loaded from database
            $this->getInitialContext()->getSystemLogger()->info(
                sprintf('Successfully reloaded data from database in stateful session bean %s', __CLASS__)
            );

        } else { // log a message that the data has already been loaded

            $this->getInitialContext()->getSystemLogger()->info(
                sprintf('Successfully loaded data from stateful session bean instance %s', __CLASS__)
            );
        }

        // return the user instance
        return $this->user;
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
