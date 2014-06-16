<?php

/**
 * TechDivision\Example\Actions\MessageQueueAction
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
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Actions;

use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Utils\ContextKeys;
use TechDivision\MessageQueueClient\MessageQueue;
use TechDivision\MessageQueueClient\QueueConnectionFactory;
use TechDivision\MessageQueueProtocol\Messages\StringMessage;
use TechDivision\Example\Utils\RequestKeys;

/**
 * Example servlet that imports .csv files by uploading them and sends a message to the
 * message queue to start the import.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class MessageQueueAction extends ExampleBaseAction
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     *
     * @var string
     */
    const MESSAGE_QUEUE_TEMPLATE = 'static/templates/messageQueue.phtml';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Loads all .csv file uploads and attaches it to the servlet context ready to be rendered
     * by the template.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // initialize an array object to load file uploads
        $overviewData = new \ArrayObject();

        // init file iterator on deployment directory
        $fileIterator = new \FilesystemIterator(ini_get('upload_tmp_dir'));

        // Iterate through all phar files and extract them to tmp dir
        foreach (new \RegexIterator($fileIterator, '/^.*\\.csv$/') as $importFile) {
            $overviewData->append($importFile->getFilename());
        }

        // set the uploaded .csv files to the context
        $this->setAttribute(ContextKeys::OVERVIEW_DATA, $overviewData);

        // render the template
        $servletResponse->appendBodyStream(
            $this->processTemplate(MessageQueueAction::MESSAGE_QUEUE_TEMPLATE, $servletRequest, $servletResponse)
        );
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function importAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // initialize the connection and the session
        $queue = MessageQueue::createQueue('queue/import');
        $connection = QueueConnectionFactory::createQueueConnection();
        $session = $connection->createQueueSession();
        $sender = $session->createSender($queue);

        // load the params with the entity data
        $filename = $servletRequest->getParameter(RequestKeys::FILENAME);

        // initialize the message with the name of the file to import the data from
        $message = new StringMessage(ini_get('upload_tmp_dir') . DIRECTORY_SEPARATOR . $filename);

        // create a new message and send it
        $send = $sender->send($message, false);

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Handles a .csv file upload by storing the uploaded file in the directory specified
     * by the php.ini configuration upload_tmp_dir.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see IndexServlet::indexAction()
     */
    public function uploadAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the uploaded file information
        $fileToUpload = $servletRequest->getPart(RequestKeys::FILE_TO_UPLOAD);

        // sample for saving file to appservers upload tmp folder with tmpname
        $fileToUpload->write(
            tempnam(ini_get('upload_tmp_dir'), 'example_upload_') . '.' . pathinfo($fileToUpload->getFilename(), PATHINFO_EXTENSION)
        );

        // after the successfull upload, render the template again
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Deletes the uploaded .csv file from the directory specified by the php.ini configuration upload_tmp_dir.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function deleteAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the params with the entity data
        $filename = $servletRequest->getParameter(RequestKeys::FILENAME);

        // delete the file from the temporary upload directory
        unlink(ini_get('upload_tmp_dir') . DIRECTORY_SEPARATOR . $filename);

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Creates and returns the URL to start the .csv import action.
     *
     * @param string $importFile The file info of the .csv file to import
     *
     * @return string The URL to start the file import
     */
    public function getImportLink($importFile)
    {
        return sprintf('index.do/messageQueue/import?%s=%s', RequestKeys::FILENAME, $importFile);
    }

    /**
     * Creates and returns the URL to delete the uploaded .csv file.
     *
     * @param string $importFile The file info of the .csv file to delete
     *
     * @return string The URL with the deletion link
     */
    public function getDeleteLink($importFile)
    {
        return sprintf('index.do/messageQueue/delete?%s=%s', RequestKeys::FILENAME, $importFile);
    }
}
