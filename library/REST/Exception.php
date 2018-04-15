<?php
/**
 * REST_Exception
 *
 * The purpose of class is to provide posibility to work with rest errors
 * through exceptions and then handle it in error controller.
 *
 * Default error http code is 400
 *
 * Example
 * <code>
 * switch ($errors->type) {
 *     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
 *     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
 *     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
 *         // 404 error -- controller or action not found
 *         $this->view->errorMessages[] = 'Page not found';
 *         $this->getResponse()->setHttpResponseCode(404);
 *         break;
 *
 *     default:
 *         // application error
 *
 *         $message = 'Application error';
 *         $httpCode = 500;
 *
 *         if (($exception = $this->getResponse()->getException())
 *             && ($exception[0] instanceof REST_Exception)
 *         ){
 *             $message = $exception[0]->getMessage();
 *             $httpCode = $exception[0]->getHttpCode();
 *         }
 *
 *         $this->view->errorMessages[] = $message;
 *         $this->getResponse()->setHttpResponseCode($httpCode);
 *         break;
 * }
 * </code>
 *
 * @author Yuriy Ishchenko <ishenkoyv@gmail.com>
 */
class REST_Exception extends Exception
{
    protected $apiCode;

    protected $debugMessage;

    public function __construct($message, $apiCode = REST_Message::ILLEGAL_REQUEST, $debugMessage = '')
    {
        parent::__construct($message);

        $this->apiCode = $apiCode;

        $this->debugMessage = $debugMessage;
    }

    public function getApiCode()
    {
        return $this->apiCode;
    }

    public function getDebugMessage()
    {
        return $this->debugMessage;
    }
}
