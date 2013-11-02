<?php

/**
 * TechDivision\Example\Services\AbstractProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\Example\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;
use TechDivision\Example\Entities\User;

/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @package   TechDivision\Example
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author    Tim Wagner <tw@techdivision.com>
 */
class AbstractProcessor
{

    /**
     * Datasource name to use.
     *
     * @var string
     */
    protected $datasourceName = 'TechDivision\Example';

    /**
     * Relative path to the folder with the database entries.
     *
     * @var string
     */
    protected $pathToEntities = 'META-INF/classes/TechDivision/Example/Entities';

    /**
     * The application instance that provides the entity manager.
     *
     * @var Application
     */
    protected $application;

    /**
     * Initializes the session bean with the Application instance.
     *
     * Checks on every start if the database already exists, if not
     * the database will be created immediately.
     *
     * @param Application $application
     *            The application instance
     *
     * @return void
     */
    public function __construct(ApplicationInterface $application)
    {
        try {

            // set the application instance and initialize the connection parameters
            $this->setApplication($application);
            $this->initConnectionParameters();

            // check if the database already exists, if not create it
            $tool = new SchemaValidator($this->getEntityManager());
            if ($tool->schemaInSyncWithMetadata() === false) {
                $this->createSchema();
            }

        } catch (\Doctrine\DBAL\DBALException $e) {
            // doesn't do anything here, because SQLite is not enabled of updating the schema
        }
    }

    /**
     * Return's the path to the doctrine entities.
     *
     * @return string The path to the doctrine entities
     */
    public function getPathToEntities()
    {
        return $this->pathToEntities;
    }

    /**
     * Return's the datasource name to use.
     *
     * @return string The datasource name
     */
    public function getDatasourceName()
    {
        return $this->datasourceName;
    }

    /**
     * The application instance providing the database connection.
     *
     * @param
     *            \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The application instance
     *
     * @return void
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * The application instance providing the database connection.
     *
     * @return Application The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The database connection parameters used to connect to Doctrine.
     *
     * @param array $connectionParameters
     *            The Doctrine database connection parameters
     *
     * @return
     *
     */
    public function setConnectionParameters(array $connectionParameters = array())
    {
        $this->connectionParameters = $connectionParameters;
    }

    /**
     * Returns the database connection parameters used to connect to Doctrine.
     *
     * @return array The Doctrine database connection parameters
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * Return's the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->getApplication()->getInitialContext();
    }

    /**
     * Return's the system configuration
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     * Return's the array with the datasources.
     *
     * @return array The array with the datasources
     */
    public function getDatasources()
    {
        return $this->getSystemConfiguration()->getDatasources();
    }

    /**
     * Return's the initialized Doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManager The initialized Doctrine entity manager
     */
    public function getEntityManager()
    {
        $pathToEntities = array(
            $this->getApplication()->getWebappPath() . DIRECTORY_SEPARATOR . $this->getPathToEntities()
        );
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($pathToEntities, true);
        return EntityManager::create($this->getConnectionParameters(), $metadataConfiguration);
    }

    /**
     * Initializes the database connection parameters necessary
     * to connect to the database using Doctrine.
     *
     * @return void
     */
    public function initConnectionParameters()
    {

        // iterate over the found database sources
        foreach ($this->getDatasources() as $datasourceNode) {

            // if the datasource is related to the session bean
            if ($datasourceNode->getName() == $this->getDatasourceName()) {

                // initialize the database node
                $databaseNode = $datasourceNode->getDatabase();

                // initialize the connection parameters
                $connectionParameters = array(
                    'driver' => $databaseNode->getDriver()
                            ->getNodeValue()
                            ->__toString(),
                    'user' => $databaseNode->getUser()
                            ->getNodeValue()
                            ->__toString(),
                    'password' => $databaseNode->getPassword()
                            ->getNodeValue()
                            ->__toString()
                );

                /*
                // initialize database driver specific connection parameters
                if (($databaseName = $databaseNode->getDatabaseName()
                    ->getNodeValue()
                    ->__toString()) != null) {
                    $connectionParameters['dbname'] = $databaseName();
                }
                */

                // initialize the path to the database when we use sqlite for example
                if (($path = $databaseNode->getPath()
                        ->getNodeValue()
                        ->__toString()) != null
                ) {
                    $connectionParameters['path']
                        = $this->getApplication()->getWebappPath() . DIRECTORY_SEPARATOR . $path;
                }

                // set the connection parameters
                $this->setConnectionParameters($connectionParameters);
            }
        }
    }

    /**
     * Deletes the database schema and creates it new.
     *
     * Attention: All data will be lost if this method has been invoked.
     *
     * @return void
     */
    public function createSchema()
    {

        // load the entity manager and the schema tool
        $entityManager = $this->getEntityManager();
        $tool = new SchemaTool($entityManager);

        // initialize the schema data from the entities
        $classes = array(
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Assertion'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Resource'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Role'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Rule'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Sample'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\User')
        );

        // drop the schema if it already exists and create it new
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        // create a default user
        $this->createDefaultUser();
    }

    /**
     * Creates the default admin user.
     *
     * @return void
     */
    protected function createDefaultUser()
    {
        try {

            // load the entity manager
            $entityManager = $this->getEntityManager();

            // set user data and save it
            $user = $this->getApplication()->newInstance('\TechDivision\Example\Entities\User');
            $user->setUserId(1);
            $user->setEmail('info@appserver.io');
            $user->setUsername('admin');
            $user->setUserLocale('en_US');
            $user->setPassword(md5('password'));
            $user->setEnabled(true);
            $user->setRate(1000);
            $user->setContractedHours(160);
            $user->setLdapSynced(false);
            $user->setSyncedAt(time());
            $entityManager->persist($user);

            // flush the entity manager
            $entityManager->flush();
        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }
}