<?php

/**
 * TechDivision\Example\Handlers\AbstractHandler
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

use Ratchet\ConnectionInterface;
use TechDivision\PersistenceContainerClient\ConnectionFactory;
use TechDivision\WebSocketServer\Handlers\AbstractHandler;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle web socket requests by subclasses action methods.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Handlers
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class BaseHandler extends AbstractHandler
{

    /**
     * Default request parameter containing the action to be invoked.
     *
     * @var string
     */
    const METHOD_NAME_PARAM = 'action';

    /**
     * Defines the persistence container application name to connect to.
     *
     * @var string
     */
    const PERSISTENCE_CONTAINER_APPLICATION_NAME = 'example';

    /**
     * The connected web socket clients.
     *
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * The persistence container connection to use.
     *
     * @var \TechDivision\PersistenceContainerClient\Interfaces\Connnection
     */
    protected $connection;

    /**
     * The persistence container session to use.
     *
     * @var \TechDivision\PersistenceContainerClient\Interfaces\Session
     */
    protected $session;

    /**
     * Initializes the message handler with the container.
     *
     * @return void
     */
    public function __construct()
    {
        // initialize the object storage for the client connections
        $this->clients = new \SplObjectStorage();

        // create proxy connnection + session
        $this->connection = ConnectionFactory::createContextConnection(BaseHandler::PERSISTENCE_CONTAINER_APPLICATION_NAME);
        $this->session = $this->connection->createContextSession();
    }

    /**
     * Creates a new proxy for the passed session bean class name
     * and returns it.
     *
     * @param string $proxyClass The session bean class name to return the proxy for
     *
     * @return mixed The proxy instance
     */
    public function getProxy($proxyClass)
    {
        $initialContext = $this->session->createInitialContext();
        return $initialContext->lookup($proxyClass);
    }

    /**
     * This method will be invoked when a new client has to be connected
     * and attaches the client to the handler.
     *
     * @param \Ratchet\ConnectionInterface $conn The ratchet connection instance
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn, 0);
    }

    /**
     * This method will be invoked when the client connection will be closed.
     *
     * @param \Ratchet\ConnectionInterface $conn The ratchet connection instance
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * The method will be invoked when an error occures
     * during client connection handling.
     *
     * @param \Ratchet\ConnectionInterface $conn The ratchet connection instance
     * @param \Exception                   $e    The exception that leads to the error
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        error_log($e->__toString());
        $conn->close();
    }

    /**
     * This method will be invoked when a new message has to be send
     * to the connected clients.
     *
     * @param \Ratchet\ConnectionInterface $from The ratchet connection instance
     * @param string                       $msg  The message to be send to all clients
     *
     * @return void
     * @see \Ratchet\MessageInterface::onMessage()
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // initialize request params and method name
        $params = array();
        parse_str($msg, $params);
        $methodName = $this->getMethodName($params);
        // prepare params and action and invoke it
        $reflectionObject = new \ReflectionObject($this);
        if ($reflectionObject->hasMethod($methodName)) {
            $reflectionMethod = $reflectionObject->getMethod($methodName);
            $result = $reflectionMethod->invokeArgs($this, $this->prepareParams($reflectionMethod, $params));
        }
        // send JSON encoded answer back to clients
        foreach ($this->clients as $client) {
            $client->send(json_encode($result));
        }
    }

    /**
     * Sorts the request params to match the action methods params
     * and strips the action param.
     *
     * @param \ReflectionMethod $reflectionMethod The reflection method to prepare the params for
     * @param array             $params           The params to prepare
     *
     * @return array The request params prepared for the reflection method
     */
    protected function prepareParams(\ReflectionMethod $reflectionMethod, array $params)
    {
        $preparedParams = array();
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $preparedParams[$reflectionParameter->getPosition()] = $params[$reflectionParameter->getName()];
        }
        return $preparedParams;
    }

    /**
     * Returns the prepared action method name and returns it.
     *
     * @param array $params The request params to prepare the action method from
     *
     * @return string The prepared action method name
     * @throws \Exception Is thrown if the param containing the action method name to invoke is missing
     */
    protected function getMethodName(array $params)
    {
        if (array_key_exists(self::METHOD_NAME_PARAM, $params)) {
            return $params[self::METHOD_NAME_PARAM] . ucfirst(self::METHOD_NAME_PARAM);
        }
        throw new \Exception('Missing action parameter in request');
    }
}
