<?php
namespace Sta\OAuthConnect\Controller\Action;

use Sta\OAuthConnect\Controller\AbstractActionExController;
use Sta\OAuthConnect\Controller\Action\Exception\InvalidAction;
use Sta\OAuthConnect\Controller\Action\Exception\InvalidActionInstanceType;

abstract class AbstractAction
{

    /**
     * @var AbstractActionExController
     */
    protected $controller;

    public static function invoke(AbstractActionExController $controller, $actionName = null)
    {
        if (!$actionName) {
            $actionName = AbstractActionExController::getMethodFromAction($controller->params()->fromRoute('action'));
        }

        $actionName      = ucfirst($actionName);
        $actionName      = preg_replace('/Action$/', '', $actionName);
        $controllerClass = str_replace('\\', DIRECTORY_SEPARATOR, get_class($controller));
        $controllerName  = str_replace('Controller', '', basename($controllerClass));
        $controllerNs    = dirname($controllerClass);
        $controllerNs    = str_replace(DIRECTORY_SEPARATOR, '\\', $controllerNs);
        $actionFqcn      = "$controllerNs\\Action\\$controllerName\\$actionName";

        if (!class_exists($actionFqcn)) {
            throw new InvalidAction('Action "' . $actionFqcn . '" does not exists.');
        }

        $parents = class_parents($actionFqcn);
        if (!in_array(self::class, $parents)) {
            throw new InvalidActionInstanceType('Action "' . $actionFqcn . '" must inherit ' . self::class . '.');
        }

        /** @var AbstractAction $action */
        $action = null;
        if ($controller->getServiceLocator()->has($actionFqcn)) {
            $action = $controller->getServiceLocator()->get($actionFqcn);
        } else {
            $action = new $actionFqcn();
        }
        $action->setController($controller);

        return $action->execute();
    }

    /**
     * Executa a ação.
     *
     * @return mixin|\Zend\Stdlib\ResponseInterface
     */
    public abstract function execute();

    /**
     * @return AbstractActionExController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $param
     * @param mixed $default
     *
     * @return mixed|\Zend\Mvc\Controller\Plugin\Params
     */
    public function params($param = null, $default = null)
    {
        return $this->getController()->params($param, $default);
    }

    /**
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest()
    {
        return $this->getController()->getRequest();
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getResponse()
    {
        return $this->getController()->getResponse();
    }

    /**
     * @param \Sta\OAuthConnect\Controller\AbstractActionExController $controller
     *
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

}
