<?php

namespace Sta\OAuthConnect\Controller;


use Sta\OAuthConnect\Controller\Action\AbstractAction;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;

/**
 * @author: Stavarengo
 */
class AbstractActionExController extends AbstractActionController
{
    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     *
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return parent::onDispatch($e);
        }

        $action = $routeMatch->getParam('action', null);

        if (!$action) {
            return parent::onDispatch($e);
        }

        $method = static::getMethodFromAction($action);

        if (method_exists($this, $method)) {
            return parent::onDispatch($e);
        }

        $actionResponse = AbstractAction::invoke($this);

        $e->setResult($actionResponse);

        return $actionResponse;
    }

    public function indexAction()
    {
        return AbstractAction::invoke($this);
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

}
