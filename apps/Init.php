<?php

class Bootstrap {
	public function Bootstrap($ConfigPath = '../apps/config/application.xml') {
		$Config = $this->getConfig($ConfigPath);
		
		Zend_Registry::set('config', $Config);
		
		$this->setLoader($Config->Loader);
	
		$frontController = $this->setFrontController($Config->FrontController); 
		$frontController->setRouter($this->setRouter($Config->Routes));
	
		$this->setView($Config->View);

		$this->setActionHelper();
		
		$this->setDataBase($Config->database);
		
		$this->setAuthAdapter($Config->auth);
		
		$this->setSmtpTransport($Config->SmtpTransport);
		
		$frontController->dispatch();
	}
	
	protected function getConfig($ConfigPath) {
		return new Zend_Config_Xml($ConfigPath, 'MadHouse');
	}
	
	protected function setLoader($config) {
		$loader = new Zend_Loader_Autoloader_Resource($config->toArray());
		return $loader;
	}
	
	protected function setActionHelper() {
		Zend_Controller_Action_HelperBroker::addHelper(new App_Helper_InitHeader());
	}

	protected function setView($config) {
		Zend_Layout::startMvc($config->toArray());

		$layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
		$view->__construct($config->toArray());
	}
	
	protected function setFrontController($config) {
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->setParams($config->Params->toArray());
		$frontController->setControllerDirectory($config->ControllerDirectory->toArray());
		return $frontController;
	}
	
	protected function setRouter($config) {
		$route = new Zend_Controller_Router_Rewrite();
		if (get_class($config)=='Zend_Config')
			$route->addConfig($config);
		return $route;
	}
	
	protected function setDataBase($config) {
		Zend_Db_Table_Abstract::setDefaultAdapter(Zend_Db::factory($config));
	}
	
	protected function setAuthAdapter($config) {
		$AuthAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table_Abstract::getDefaultAdapter());
		$AuthAdapter
			->setTableName($config->tableName)
			->setIdentityColumn($config->identityColumn)
			->setCredentialColumn($config->credentialColumn)
			->setCredentialTreatment($config->credentialTreatment);

		Zend_Registry::set('AuthAdapter', $AuthAdapter);
	}
	
	protected function setSmtpTransport($config) {
		$SmtpTransport = new Zend_Mail_Transport_Smtp($config->host, $config->toArray());
		Zend_Mail::setDefaultTransport($SmtpTransport);
	}
}