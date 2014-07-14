<?php

/**
 * TechDivision\Example\Services\AbstractProcessor
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

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use TechDivision\Example\Entities\User;
use TechDivision\Application\Interfaces\ApplicationInterface;

/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage MessageBeans
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AbstractProcessor
{

    /**
     * Datasource name to use.
     *
     * @var string
     */
    protected $datasourceName = 'appserver.io-example-application';

    /**
     * Relative path to the folder with the database entries.
     *
     * @var string
     */
    protected $pathToEntities = 'META-INF/classes/TechDivision/Example/Entities';

    /**
     * The application instance that provides the entity manager.
     *
     * @var \TechDivision\ApplicationServer\Interfaces\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the session bean with the Application instance.
     *
     * Checks on every start if the database already exists, if not
     * the database will be created immediately.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->setApplication($application);
        $this->initConnectionParameters();
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
     * @param \TechDivision\ApplicationServer\Interfaces\ApplicationInterface $application The application instance
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
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The database connection parameters used to connect to Doctrine.
     *
     * @param array $connectionParameters The Doctrine database connection parameters
     *
     * @return void
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
     * Returns the initial context instance.
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
        // prepare the path to the entities
        $absolutePaths = array();
        if ($relativePaths = $this->getPathToEntities()) {
            foreach (explode(PATH_SEPARATOR, $relativePaths) as $relativePath) {
                $absolutePaths[] = $this->getApplication()->getWebappPath() . DIRECTORY_SEPARATOR . $relativePath;
            }
        }
        // create the database configuration and initialize the entity manager
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($absolutePaths, true);
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
                    'driver'   => $databaseNode->getDriver()->getNodeValue()->__toString(),
                    'user'     => $databaseNode->getUser()->getNodeValue()->__toString(),
                    'password' => $databaseNode->getPassword()->getNodeValue()->__toString()
                );

                // initialize the path to the database when we use sqlite for example
                if ($path = $databaseNode->getPath()->getNodeValue()->__toString()) {
                    $connectionParameters['path'] = $this->getApplication()->getWebappPath() . DIRECTORY_SEPARATOR . $path;
                }

                // set the connection parameters
                $this->setConnectionParameters($connectionParameters);
            }
        }
    }
}
