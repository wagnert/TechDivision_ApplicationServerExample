<?php

/**
 * TechDivision\Example\MessageBeans\ImportReceiver
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
 * @subpackage MessageBeans
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\MessageBeans;

use TechDivision\MessageQueueClient\Interfaces\Message;
use TechDivision\MessageQueueClient\Messages\MessageMonitor;
use TechDivision\MessageQueueClient\Receiver\AbstractReceiver;
use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

/**
 * An message receiver that imports data chunks into a database.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage MessageBeans
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ImportChunkReceiver extends AbstractReceiver
{

    /**
     * Will be invoked when a new message for this message bean will be available.
     * 
     * @param \TechDivision\MessageQueueClient\Interfaces\Message $message   A message this message bean is listen for
     * @param string                                              $sessionId The session ID                            
     *
     * @return void
     * @see \TechDivision\MessageQueueClient\Interfaces\MessageReceiver::onMessage()
     */
    public function onMessage(Message $message, $sessionId)
    {

        // put status message
        error_log($logMessage = "Process chuck data message");

        $connection = Factory::createContextConnection();
        $session = $connection->createContextSession();
        $initialContext = $session->createInitialContext();

        // lookup the remote processor implementation
        $processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');

        // read in message chunk data
        $chunkData = $message->getMessage();

        // import the data found in the file
        foreach ($chunkData as $data) {

            // explode the name parts and append the data in the database
            list ($firstname, $lastname) = explode(',', $data);

            // prepare the entity
            $entity = new Sample();
            $entity->setName($firstname . ', ' . $lastname);

            // store the entity in the database
            $processor->persist($entity);
        }
    }
}
