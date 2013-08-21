<?php

/**
 * TechDivision\Example\Services\UserProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Services;

use TechDivision\Example\Entities\User;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\PersistenceContainer\Application;
use TechDivision\PersistenceContainer\Interfaces\Stateless;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;

/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 * 
 * @Stateless
 */
class UserProcessor implements Stateless {

    /**
     * The application instance that provides the entity manager.
     * @var Application
     */
    protected $application;

    /**
     * Initializes the session bean with the Application instance.
     *
     * Checks on every start if the database already exists, if not
     * the database will be created immediately.
     *
     * @param Application $application The application instance
     * @return void
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }

    /**
     * The application instance providing the database connection.
     *
     * @return Application The application instance
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * Validates the passed username agains the password.
     *
     * @param string $username
     * @param string $password
     * @throws \Exception Is thrown if the user with the passed username doesn't exist or match the password
     */
    public function login($username, $password) {

        // load the entity manager and the user repository
        $entityManager = $this->getApplication()->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\User');

        // try to load the user
        $user = $repository->findOneBy(array('username' => $username));
        if ($user == null) {
            throw new \Exception("User doesn't exists");
        }
        if ($user->getPassword() !== md5($password)) {
            throw new \Exception("Username doesn't match password");
        }
    }
}