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
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage MessageBeans
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ImportReceiver extends AbstractReceiver
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
            $i ++;
            
            // fill chunk data array
            $chunkData[] = $data;
            // check if chunk size is reached
            if ($i == $chunkSize) {
                
                // raise the counter for the chunks
                $currentChunkIndex ++;
                
                // reset counter
                $i = 0;
                
                // send chunked data message
                $send = $sender->send(new ArrayMessage($chunkData), false);
                
                // reset chunk data
                $chunkData = array();
                
                // update the MessageMonitor
                $this->updateMonitor($message);
                error_log(__METHOD__ . ' -> chunk ' . $currentChunkIndex);
                sleep(5);
            }
        }
    }
}
