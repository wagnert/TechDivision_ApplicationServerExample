<?php

/**
 * TechDivision\Example\Entities\Role
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
 * @subpackage Entities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Entities;

/**
 * Doctrine entity that represents a role.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Entities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 *
 * @Entity @Table(name="role")
 */
class Role
{

    /**
     * @var int
     * @Id @GeneratedValue @Column(type="integer")
     */
    protected $roleId;

    /**
     * @var integer
     * @Column(type="integer")
     */
    protected $roleIdFk;

    /**
     * @var integer
     * @Column(type="integer")
     */
    protected $userIdFk;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $name;

    /**
     * Returns the value of the class member roleId.
     *
     * @return integer Holds the value of the class member roleId
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Sets the value for the class member roleId.
     *
     * @param integer $roleId Holds the value for the class member roleId
     * 
     * @return void
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Returns the value of the class member roleIdFk.
     *
     * @return integer Holds the value of the class member roleIdFk
     */
    public function getRoleIdFk()
    {
        return $this->roleIdFk;
    }

    /**
     * Sets the value for the class member roleIdFk.
     *
     * @param integer $roleIdFk Holds the value for the class member roleIdFk
     * 
     * @return void
     */
    public function setRoleIdFk($roleIdFk = null)
    {
        $this->roleIdFk = $roleIdFk;
    }

    /**
     * Returns the value of the class member userIdFk.
     *
     * @return integer Holds the value of the class member userIdFk
     */
    public function getUserIdFk()
    {
        return $this->userIdFk;
    }

    /**
     * Sets the value for the class member userIdFk.
     *
     * @param integer $userIdFk Holds the value for the class member userIdFk
     * 
     * @return void
     */
    public function setUserIdFk($userIdFk = null)
    {
        $this->userIdFk = $userIdFk;
    }

    /**
     * Returns the value of the class member name.
     *
     * @return string Holds the value of the class member name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value for the class member name.
     *
     * @param string $name Holds the value for the class member name
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
