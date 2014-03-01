<?php

/**
 * TechDivision\Example\Handlers\SampleHandler
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
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Handlers;

use TechDivision\Example\Entities\Sample;

/**
 * This is a web socket handler that handles requests
 * related with samples.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class SampleHandler extends BaseHandler
{

    /**
     * Class name of the persistence container proxy that handles the data.
     * 
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\SampleProcessor';

    /**
     * Persists the sample entity with the passed data.
     *
     * @param string $sampleId The ID to be persisted
     * @param string $name The name to be persisted
     * 
     * @return \TechDivision\Example\Entities\Sample The persisted entity
     */
    public function persistAction($sampleId, $name)
    {
        // create a new entity and persist it
        $entity = new Sample();
        $entity->setSampleId((integer) $sampleId);
        $entity->setName($name);

        // store and return the entity
        return array($this->getProxy(self::PROXY_CLASS)->persist($entity));
    }

    /**
     * Returns all sample entities.
     *
     * @return array The array with the sample entities
     */
    public function overviewAction()
    {
        return $this->getProxy(self::PROXY_CLASS)->findAll();
    }
}
