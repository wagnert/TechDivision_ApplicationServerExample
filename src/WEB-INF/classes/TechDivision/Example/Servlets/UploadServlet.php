<?php

/**
 * TechDivision\Example\Servlets\UploadServlet
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
 * @subpackage Http
 * @author     Johann Zelger <jz@techdivision.com>
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\Example\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Http\ServletRequest;
use TechDivision\ServletContainer\Http\ServletResponse;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\Example\Servlets\AbstractServlet;
use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Utils\ContextKeys;

/**
 * Example servlet implementation that handles an upload request.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Servlets
 * @author     Johann Zelger <jz@techdivision.com>
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class UploadServlet extends AbstractServlet
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     * 
     * @var string
     */
    const UPLOAD_TEMPLATE = 'static/templates/upload.phtml';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Renders an upload dialoge with a select and submit button.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return void
     */
    public function indexAction(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $servletResponse->setContent($this->processTemplate(self::UPLOAD_TEMPLATE, $servletRequest, $servletResponse));
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see IndexServlet::indexAction()
     */
    public function uploadAction(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
    	
    	// sample for saving file to appservers upload tmp folder with tmpname
    	$servletRequest->getPart('fileToUpload')->write(
			tempnam(ini_get('upload_tmp_dir'), 'example_upload_')
    	);
    
    	// after the successfull upload, render the template again
        $this->indexAction($servletRequest, $servletResponse);
    }
}
