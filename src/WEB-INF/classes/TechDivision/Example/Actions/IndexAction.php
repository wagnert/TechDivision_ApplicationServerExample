<?php

/**
 * TechDivision\Example\Actions\IndexAction
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

/**
 * Example action implementation that loads data over a persistence container proxy
 * and renders a list, based on the returned values.
 *
 * Additional it provides functionality to edit, delete und persist the data after
 * changing it.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerExample
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class IndexAction extends ExampleBaseAction
{

    /**
     * The relative path, up from the webapp path, to the template to use.
     *
     * @var string
     */
    const INDEX_TEMPLATE = 'static/templates/index.phtml';

    /**
     * Class name of the persistence container proxy that handles the data.
     *
     * @var string
     */
    const PROXY_CLASS = 'TechDivision\Example\Services\SampleProcessor';

    /**
     * Default action to invoke if no action parameter has been found in the request.
     *
     * Loads all sample data and attaches it to the servlet context ready to be rendered
     * by the template.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $overviewData = $this->getProxy(IndexAction::PROXY_CLASS)->findAll();
        $this->setAttribute(ContextKeys::OVERVIEW_DATA, $overviewData);
        $servletResponse->appendBodyStream($this->processTemplate(IndexAction::INDEX_TEMPLATE, $servletRequest, $servletResponse));
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
    public function loadAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the params with the entity data
        $parameterMap = $servletRequest->getParameterMap();

        // check if the necessary params has been specified and are valid
        if (!array_key_exists('sampleId', $parameterMap)) {
            throw new \Exception();
        } else {
            $sampleId = filter_var($parameterMap['sampleId'], FILTER_VALIDATE_INT);
        }

        // load the entity to be edited and attach it to the servlet context
        $viewData = $this->getProxy(IndexAction::PROXY_CLASS)->load($sampleId);
        $this->setAttribute(ContextKeys::VIEW_DATA, $viewData);

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Deletes the sample entity with the sample ID found in the request and
     * reloads all other entities from the database.
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
        $parameterMap = $servletRequest->getParameterMap();

        // check if the necessary params has been specified and are valid
        if (!array_key_exists('sampleId', $parameterMap)) {
            throw new \Exception();
        } else {
            $sampleId = filter_var($parameterMap['sampleId'], FILTER_VALIDATE_INT);
        }

        // delete the entity
        $this->getProxy(IndexAction::PROXY_CLASS)->delete($sampleId);

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Persists the entity data found in the request.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     * @see \TechDivision\Example\Servlets\IndexServlet::indexAction()
     */
    public function persistAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the params with the entity data
        $parameterMap = $servletRequest->getParameterMap();

        // check if the necessary params has been specified and are valid
        if (!array_key_exists('sampleId', $parameterMap)) {
            throw new \Exception();
        } else {
            $sampleId = filter_var($parameterMap['sampleId'], FILTER_VALIDATE_INT);
        }
        if (!array_key_exists('name', $parameterMap)) {
            throw new \Exception();
        } else {
            $name = filter_var($parameterMap['name'], FILTER_SANITIZE_STRING);
        }

        // create a new entity and persist it
        $entity = new Sample();
        $entity->setSampleId((integer) $sampleId);
        $entity->setName($name);
        $this->getProxy(IndexAction::PROXY_CLASS)->persist($entity);

        // reload all entities and render the dialog
        $this->indexAction($servletRequest, $servletResponse);
    }

    /**
     * Creates and returns the URL to open the dialog to edit the passed entity.
     *
     * @param \TechDivision\Example\Entities\Sample $entity The entity to create the edit link for
     *
     * @return string The URL to open the edit dialog
     */
    public function getEditLink(Sample $entity)
    {
        return 'index.do/index/load?sampleId=' . $entity->getSampleId();
    }

    /**
     * Creates and returns the URL that has to be invoked to delete the passed entity.
     *
     * @param \TechDivision\Example\Entities\Sample $entity The entity to create the deletion link for
     *
     * @return string The URL with the deletion link
     */
    public function getDeleteLink(Sample $entity)
    {
        return 'index.do/index/delete?sampleId=' . $entity->getSampleId();
    }
}
