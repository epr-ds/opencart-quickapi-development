<?php

class ControllerExtensionModuleQuickapi extends Controller
{
	const MODULE_CODE = 'quickapi';

	const FORBIDDEN_NAMES = ['cart', 'coupon', 'currency', 'customer', 'login', 'order', 'payment', 'reward', 'shipping', 'voucher'];

	const API_TEMPLATES = [
		'controller'  	=> DIR_APPLICATION . 'controller/extension/module/quickapi/controller.php',
		'model' 		=> DIR_APPLICATION . 'controller/extension/module/quickapi/model.php'
	];

	const API_FILES = [
		'controller' => DIR_CATALOG . 'controller/api/%s.php',
		'model'		 => DIR_CATALOG . 'model/extension/module/quickapi/%s.php',
		'lang'		 => DIR_CATALOG . 'language/en-gb/api/%s.php'
	];

	private $error = array();

	public function index()
	{
		$this->load->model('setting/module');
		$this->load->language('extension/module/quickapi');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$data = $this->processData($this->request->get['module_id']);
			
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule(self::MODULE_CODE, $data);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $data);
			}
			
			$this->buildAPI($data);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->post['name']);

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data = array();

		if (!empty($this->error)){
			$data['error'] = $this->error;
			if ($this->error['api_name_forbidden']){
			}
		}
		
		// Load module setting
		if (isset($this->request->get['module_id'])){
			$setting = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		
		# Help for forbidden names
		$names = "";
		foreach(self::FORBIDDEN_NAMES as $name){
			$names .= "{$name}, ";
		}
		$names = trim($names, ", ");
		$data['help_api_name_forbidden'] = sprintf($this->language->get('help_api_name_forbidden'), $names);

		# Check if module is a new one.
		$data['is_new'] = !isset($this->request->get['module_id']);

		# Module name
		if (isset($this->request->post['name'])){
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($setting)) {
			$data['name'] = $setting['name'];
		} else {
			$data['name'] = '';
		}
		
		# API name
		if (isset($this->request->post['api_name'])) {
			$data['api_name'] = $this->request->post['api_name'];
		} elseif (!empty($setting)) {
			$data['api_name'] = $setting['api_name'];
		} else {
			$data['api_name'] = '';
		}

		# Status
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($setting)) {
			$data['status'] = $setting['status'];
		} else {
			$data['status'] = 1;
		}

		# Description
		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($setting)) {
			$data['description'] = $setting['description'];
		} else {
			$data['description'] = '';
		}
		
		# Code controller
		if (isset($this->request->post['code_controller'])) {
			$data['code_controller'] = $this->request->post['code_controller'];
		} elseif (!empty($setting)) {
			$data['code_controller'] = $setting['code']['controller'];
		} else {
			$data['code_controller'] = '';
		}		
		
		# Code model
		if (isset($this->request->post['code_model'])) {
			$data['code_model'] = $this->request->post['code_model'];
		} elseif (!empty($setting)) {
			$data['code_model'] = $setting['code']['model'];
		} else {
			$data['code_model'] = '';
		}
		
		# Code lang
		if (isset($this->request->post['code_lang'])) {
			$data['code_lang'] = $this->request->post['code_lang'];
		} elseif (!empty($setting)) {
			$data['code_lang'] = $setting['code']['lang'];
		} else {
			$data['code_lang'] = '';
		}

		#Breadcrumbs
		$data['breadcrumbs'] = array();

		# Breadcrumb Dashboard
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		# Breadcrumb Extensions
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];

		# Breadcrumb QuickApi
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/quickapi', 'user_token=' . $this->session->data['user_token'])
		];

		$data['error'] = $this->error;

		# Buttons actions
		$data['action']['save'] = "";
		$data['action']['build'] = html_entity_decode($this->url->link('extension/module/quickapi/build', 'user_token=' . $this->session->data['user_token'] . '&module_id='.$this->request->get['module_id']));
		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		// View components
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$htmlOutput = $this->load->view('extension/module/quickapi', $data);
		$this->response->setOutput($htmlOutput);
	}

	public function install()
	{
		$this->load->model('setting/setting');

		# Update extension settings.
		$this->model_setting_setting->editSetting("module_".self::MODULE_CODE, ['module_'.self::MODULE_CODE.'_status' => 1]);

		# Create directory for storing models
		mkdir(DIR_CATALOG . 'model/extension/module/quickapi/', 755);
	}

	public function uninstall()
	{
		$this->load->model('setting/module');
		$this->load->model('setting/setting');

		$modules = $this->model_setting_module->getModulesByCode(self::MODULE_CODE);
		
		# Delete generated files
		foreach($modules as $module){
			$setting = json_decode($module['setting'], true);
			$this->clean($setting['files']);
		}
		
		#Delete submodules from DB.
		$this->model_setting_module->deleteModulesByCode(self::MODULE_CODE);

		#Update extension settings.
		$this->model_setting_setting->editSettingValue("module_".self::MODULE_CODE, "module_quickapi_status", 0);

		# Remove directory for models.
		rmdir(DIR_CATALOG . 'model/extension/module/quickapi/');
	}

	protected function clean($files)
	{
		foreach($files as $_ => $file){
			if (file_exists($file)){
				unlink($file);
			}
		}
	}

	protected function processData($module_id)
	{
		if (isset($module_id)){
			$setting = $this->model_setting_module->getModule($module_id);
			$this->clean($setting['files']);
		}
		/* DATA RECEIVED.
			name
			api_name
			status
			description
			code_controller
			code_model
			code_lang
		*/
		$api = strtolower($this->request->post['api_name']);
		return [
			'name' 			=> $this->request->post['name'],
			'api_name' 		=> $api,
			'status'		=> (int)$this->request->post['status'],
			'description' 	=> $this->request->post['description'], 
			'files' 		=> [
				'controller' => sprintf(self::API_FILES['controller'], $api),
				'model' 	 => sprintf(self::API_FILES['model'], $api),
				'lang' 		 => sprintf(self::API_FILES['lang'], $api)
			],
			'code'			=> [
				'controller' => html_entity_decode($this->request->post['code_controller']),
				'model' => html_entity_decode($this->request->post['code_model']),
				'lang' => html_entity_decode($this->request->post['code_lang'])
			]
		];
	}
	
	protected function buildCodeFor($archive, $data)
	{
		if ($archive == 'controller' || $archive == 'model'){
			$template = file_get_contents(self::API_TEMPLATES[$archive]);
			$class = $data['api_name'];
			$class[0] = strtoupper($class[0]);
			$code = sprintf($template, $class, $data['code'][$archive]);
			file_put_contents($data['files'][$archive], $code);
		} else {
			file_put_contents($data['files'][$archive], $data['code'][$archive]);
		}
	}

	protected function buildAPI($data)
	{
		if (!$data['status'])
			return;

		$this->buildCodeFor('controller', $data);

		if (utf8_strlen($data['code']['model'])){
			$this->buildCodeFor('model', $data);
		}
		
		if (utf8_strlen($data['code']['lang'])){
			$this->buildCodeFor('lang', $data);
		}
	}

	public function build()
	{
		$this->load->model('setting/module');
		$this->load->language('extension/module/quickapi');

		$json = array();

		$data = $this->processData($this->request->get['module_id']);
		
		$this->model_setting_module->editModule($this->request->get['module_id'], $data);
		
		$this->buildAPI($data);

		$json['success'] = $this->language->get('text_built');
		
		http_response_code(200);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/quickapi')) {
			$this->error['permission'] = true;
			return false;
		}

		if (!utf8_strlen($this->request->post['name'])) {
			$this->error['name'] = true;
		}

		if(!preg_match("/^[a-z]+$/", $this->request->post['api_name'])) {
			$this->error['api_name_blank'] = true;
		}
		
		if (in_array(strtolower($this->request->post['api_name']), self::FORBIDDEN_NAMES)){
			$this->error['api_name_forbidden'] = true;
		}
		
		if (!isset($this->request->post['status'])) {
			$this->error['status'] = true;
		}
		
		if (!utf8_strlen($this->request->post['code_controller'])) {
			$this->error['code'] = true;
		}

		return empty($this->error);
	}
}
