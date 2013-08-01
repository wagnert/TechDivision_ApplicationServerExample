<?php

/**
 * TechDivision\Example\MessageBeans\ImportReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\MessageBeans;

use TechDivision\MessageQueueClient\Interfaces\Message;
use TechDivision\MessageQueueClient\Messages\ArrayMessage;
use TechDivision\MessageQueueClient\Receiver\AbstractReceiver;
use TechDivision\MessageQueueClient\Messages\MessageMonitor;
use TechDivision\Example\Entities\Sample;
use TechDivision\MessageQueueClient\Queue;
use TechDivision\MessageQueueClient\QueueConnectionFactory;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

/**
 * This is the implementation of a import message receiver.
 *
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ImportReceiver extends AbstractReceiver {
	
	/**
	 * @see \TechDivision\MessageQueueClient\Interfaces\MessageReceiver::onMessage()
	 */
	public function onMessage(Message $message, $sessionId) {

	    // log that a Message was received
		error_log($logMessage = "Successfully received / finished message");

		// define the import file from message
		$importFile = $message->getMessage();
		
		// open the import file
		$importData = file($importFile, FILE_USE_INCLUDE_PATH);

        // initialize the connection and the session
        $queue = Queue::createQueue("queue/import_chunk");
        $connection = QueueConnectionFactory::createQueueConnection();
        $session = $connection->createQueueSession();
        $sender = $session->createSender($queue);

        // init chunk data
        $chunkSize = 1000;
        $currentChunkIndex = 0;
        $chunkData = array();

        $i = 0;
        // send chunk message
        foreach ($importData as $data) {
            // increase counter
            $i++;

            // fill chunk data array
            $chunkData[] = $data;
            // check if chunk size is reached
            if ($i == $chunkSize) {
                $currentChunkIndex++;
                // reset counter
                $i = 0;
                // send chunked data message
                $send = $sender->send(new ArrayMessage($chunkData), false);
                // reset chunk data
                $chunkData = array();
                // initialize the message monitor
                /*
                $message->setMessageMonitor($monitor = new MessageMonitor(1, 'Dummy message'));
                $monitor->setRowCount(1);
                */
                // update the MessageMonitor
                $this->updateMonitor($message);
                error_log(__METHOD__ . ' -> chunk ' . $currentChunkIndex);
                sleep(5);
            }
        }

	}
}