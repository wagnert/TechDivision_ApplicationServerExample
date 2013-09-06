<?php

/**
 * TechDivision\Example\Servlets\UploadServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\Example\Servlets\AbstractServlet;
use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Utils\ContextKeys;

/**
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class UploadServlet extends AbstractServlet implements Servlet {

    /**
     * The relative path, up from the webapp path, to the template to use.
     * @var string
     */
    const UPLOAD_TEMPLATE = 'static/templates/upload.phtml';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Renders an upload dialoge with a select and submit button.
     *
     * @param Request $req The request instance
     * @param Response $res The response instance
     * @return void
     */
    public function indexAction(Request $req, Response $res) {
        $res->setContent($this->processTemplate(self::UPLOAD_TEMPLATE, $req, $res));
    }

    /**
     * Loads the sample entity with the sample ID found in the request and attaches
     * it to the servlet context ready to be rendered by the template.
     *
     * @param Request $req The request instance
     * @param Response $res The response instance
     * @return void
     * @see IndexServlet::indexAction()
     */
    public function uploadAction(Request $req, Response $res) {
    	
    	// sample for saving file to appservers upload tmp folder with tmpname
    	$req->getPart('fileToUpload')->write(
			tempnam(ini_get('upload_tmp_dir'), 'example_upload_')
    	);
    
        $this->indexAction($req, $res);
    }
}