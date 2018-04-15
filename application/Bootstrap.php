<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
    protected function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
		echo 132;exit;
        // set custom request object
        $frontController->setRequest(new REST_Request);
        $frontController->setResponse(new REST_Response);

        // register the RestHandler plugin
        $frontController->registerPlugin(new REST_Controller_Plugin_RestHandler($frontController));

        // add JSON contextSwitch helper
        $contextSwitch = new REST_Controller_Action_Helper_ContextSwitch();
        Zend_Controller_Action_HelperBroker::addHelper($contextSwitch);

        // add restContexts helper
        $restContexts = new REST_Controller_Action_Helper_RestContexts();
        Zend_Controller_Action_HelperBroker::addHelper($restContexts);
    }



	protected function _initTranslate ()

    {

        $options = $this->getOption('resources');

        $options = $options['translate'];

        if (! isset($options['data'])) {

            throw new Zend_Application_Resource_Exception(

           '对不起,没有找到语言文件！');

        }

        $adapter = isset($options['adapter']) ? $options['adapter'] : Zend_Translate::AN_ARRAY;

        $session = new Zend_Session_Namespace('aa');

        if ($session->locale) {

            $locale = $session->locale;

        } else {

            $locale =isset($options['locale']) ? $options['locale'] :null;

        }

        $data = '';

        if (isset($options['data'][$locale])) {

            $data = $options['data'][$locale];

        }

        $translateOptions =isset($options['options']) ? $options['options'] :array();

        $translate =new Zend_Translate($adapter, $data, $locale, 

        $translateOptions);

        Zend_Form::setDefaultTranslator($translate);

        Zend_Registry::set('Zend_Translate', $translate);

        return $translate;

    }

}
