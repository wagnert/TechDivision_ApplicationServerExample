<?php

/**
 * TechDivision\Example\MessageBeans\CreateATimer
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

use TechDivision\Lang\String;
use TechDivision\EnterpriseBeans\TimerInterface;
use TechDivision\MessageQueueProtocol\Message;
use TechDivision\MessageQueue\Receiver\AbstractReceiver;
use TechDivision\PersistenceContainer\TimerServiceContext;

/**
 * This is the implementation of a message bean that simply creates and starts a single
 * action timer.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage MessageBeans
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 *
 * @MessageDriven
 */
class CreateAIntervalTimer extends AbstractReceiver
{

    /**
     * Will be invoked when a new message for this message bean will be available.
     *
     * @param \TechDivision\MessageQueueProtocol\Message $message   A message this message bean is listen for
     * @param string                                     $sessionId The session ID
     *
     * @return void
     * @see \TechDivision\MessageQueueProtocol\Receiver::onMessage()
     */
    public function onMessage(Message $message, $sessionId)
    {

        // load the timer service
        $timerServiceRegistry = $this->getApplication()->getManager(TimerServiceContext::IDENTIFIER);
        $timerService = $timerServiceRegistry->locate(__CLASS__);

        // our single action timer should be invoked 10 seconds from now, every 1 second
        $initialExpiration = 10000000;
        $intervalDuration = 1000000;

        // we create the interval timer
        $timerService->createIntervalTimer($initialExpiration, $intervalDuration, new String($message->getMessage()));

        // log a message that the single action timer has been successfully created
        $this->getApplication()->getInitialContext()->getSystemLogger()->info(
            sprintf(
                'Successfully created a interval timer starting in %d seconds and a interval of %d seconds',
                $initialExpiration / 1000000,
                $intervalDuration / 1000000
            )
        );

        // update the message monitor for this message
        $this->updateMonitor($message);
    }

    /**
     * Invoked by the container upon timer expiration.
     *
     * @param \TechDivision\EnterpriseBeans\TimerInterface $timer Timer whose expiration caused this notification
     *
     * @return void
     * @Timeout
     **/
    public function timeout(TimerInterface $timer)
    {

        // log a message with the directory name we found
        $this->getApplication()->getInitialContext()->getSystemLogger()->info(
            sprintf(
                '%s has successfully been invoked by @Timeout annotation to watch directory %s',
                __METHOD__,
                $timer->getInfo()
            )
        );
    }
}
