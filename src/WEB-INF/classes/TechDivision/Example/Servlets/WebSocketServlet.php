<?php

/**
 * TechDivision\Example\Servlets\WebSocketServlet
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

use TechDivision\ServletContainer\Http\ServletRequest;
use TechDivision\ServletContainer\Http\ServletResponse;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\Example\Servlets\AbstractServlet;

/**
 * Example servlet implementation that renders a template the opens a 
 * web socket connection.
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
class WebSocketServlet extends AbstractServlet
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     * 
     * @var string
     */
    const WEBSOCKET_TEMPLATE = 'static/templates/websocket.phtml';
    
    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Loads all sample data and attaches it to the servlet context ready to be rendered
     * by the template.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return void
     */
    public function indexAction(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $servletResponse->setContent($this->processTemplate(self::WEBSOCKET_TEMPLATE, $servletRequest, $servletResponse));
    }
}
