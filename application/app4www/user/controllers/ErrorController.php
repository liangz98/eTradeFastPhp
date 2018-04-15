<?php   
class ErrorController extends Zend_Controller_Action
{
    function errorAction()
    {  
    	$errors = $this->_getParam('error_handler');
    	if(is_object($errors))
        {
            switch($errors->type)
            {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
     		
                    # 404 error -- controller or action not found 
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->view->message = 'Page not found';
                    break;
                default:
                    # application error
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->view->message = 'Application error';
                    break;
            }
     		
            $this->view->title      = 'Errors';
            $this->view->env        = $this->getInvokeArg('env');
            $this->view->exception  = $errors->exception;
            $this->view->request    = $errors->request;
        }
        else
        {
            $this->view->title      = 'No Errors';
            $this->view->message    = 'No Errors Detected';
        }
        $content = $this->view->render('default/error/error.phtml');
        echo $content;
        exit;
    }
    
    function deniedAction()
    {
    	
    }
}