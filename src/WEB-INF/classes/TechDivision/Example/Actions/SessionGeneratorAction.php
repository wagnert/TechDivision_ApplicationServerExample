<?php

/**
 * TechDivision\Example\Actions\SessionGenerator
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

use TechDivision\Servlet\SessionUtils;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

/**
 * Example servlet implementation that validates passed user credentials against
 * persistence container proxy and stores the user data in the session.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class SessionGeneratorAction extends ExampleBaseAction
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     *
     * @var string
     */
    const SESSION_GENERATOR_TEMPLATE = 'static/templates/sessionGenerator.phtml';

    /**
     * The generted session ID.
     *
     * @var string
     */
    protected $sessionId;

    /**
     * Default action to create an endless number of session (for performance testing purposes only).
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        // create a random session and start it
        $servletRequest->setRequestedSessionName('example_' . SessionUtils::generateRandomString());
        $session = $servletRequest->getSession(true);

        // write the session to the member varialbe
        $this->sessionId = $session->getId();

        // render the template
        $servletResponse->appendBodyStream($this->processTemplate(SessionGeneratorAction::SESSION_GENERATOR_TEMPLATE, $servletRequest, $servletResponse));
    }

    /**
     * Returns the generated session ID.
     *
     * @return string The session ID
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
}
