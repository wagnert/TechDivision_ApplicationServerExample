<?php

/**
 * TechDivision\Example\MessageBeans\CreateASingleActionTimer
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

use TechDivision\MessageQueueProtocol\Message;
use TechDivision\MessageQueue\Receiver\AbstractReceiver;
use TechDivision\EnterpriseBeans\TimerInterface;
use TechDivision\EnterpriseBeans\TimedObjectInterface;

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
class CreateASingleActionTimer extends AbstractReceiver implements TimedObjectInterface
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
        $timerService = $timerServiceRegistry->locate(substr(strrchr(__CLASS__, '\\'), 1));

        // our single action timer should be invoked 60 seconds from now
        $duration = 60000000;

        // we create a single action timer
        $timerService->createSingleActionTimer($duration);

        // log a message that the single action timer has been successfully created
        $this->getApplication()->getInitialContext()->getSystemLogger()->info(
            sprintf('Successfully created a single action timer with a duration of %d seconds', $duration)
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
     **/
    public function timeout(TimerInterface $timer)
    {
        $this->getApplication()->getInitialContext()->getSystemLogger()->info(
            sprintf('%s has successfully been by interface', __METHOD__)
        );
    }
}
