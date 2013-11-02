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

use TechDivision\Example\Exceptions;

/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @package        TechDivision\Example
 * @copyright      Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license        http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author         Tim Wagner <tw@techdivision.com>
 * @Stateless
 */
class UserProcessor extends AbstractProcessor
{

    /**
     * Validates the passed username agains the password.
     *
     * @param string $username
     * @param string $password
     *
     * @throws \Exception Is thrown if the user with the passed username doesn't exist or match the password
     */
    public function login($username, $password)
    {
        // load the entity manager and the user repository
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\User');

        // try to load the user
        $user = $repository->findOneBy(array('username' => $username));
        if ($user == null) {
            throw new Exceptions\LoginException("Username or Password doesn't match");
        }
        if ($user->getPassword() !== md5($password)) {
            throw new Exceptions\LoginException("Username or Password doesn't match");
        }
    }
}