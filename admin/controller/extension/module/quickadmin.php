<?php

/**
 * Name: Controller REST API QuickAdmin
 * Version: 1.0.0.0
 * Author: PerezRE
 * Last Date Updated: Jul 16, 2019
 * Description: REST API for OpenCart QuickAdmin WinForms APP
 *  */

class ControllerExtensionModuleQuickadmin extends Controller
{
	const MODULE_NAME = 'module_quickadmin';

	const API_FILE = DIR_CATALOG . 'controller/api/quickadmin.php';
	const API_ASSETS = DIR_APPLICATION . 'controller/extension/module/quickadmin_assets/';
	const API_TEMP = self::API_ASSETS . 'api.php';

	const DEFAULT_MODULE_SETTINGS = [
		[ // Products API Module
			'name'		=>	'Product API',
			'status'	=>	1, // Enabled by default
			'code'		=>	self::API_ASSETS . 'products.php'
		],
		[ // Customer API Module
			'name'		=>	'Customer API',
			'status'	=>	1, // Enabled by default
			'code'		=>	self::API_ASSETS . 'customer.php'
		],
		[ // Order API Module
			'name'		=>  'Order API',
			'status'	=>  1, // Enabled by defualt
			'code'		=>	self::API_ASSETS . 'order.php'
		],
		[ // Global Data API Module
			'name'		=>  'Server API',
			'status'	=>  1, // Enabled by defualt
			'code'		=>	self::API_ASSETS . 'global.php'
		],
		// More modules can be easily added ;)
	];

	private $error = array();

	public function index()
	{ // Add new API Module
		if (!isset($this->request->get['module_id'])) {
			$this->load->language('extension/module/quickadmin');
			$module_id = $this->addModule([
				'name' 		=> $this->language->get('text_new_module'),
				'status' 	=> 0,
				'code'		=> ''
			]);
			$this->response->redirect($this->url->link('extension/module/quickadmin','&user_token='.$this->session->data['user_token'].'&module_id='.$module_id));
		} else {
		   $this->editModule($this->request->get['module_id']);
		}
	}

	private function compileAPI()
	{
		$this->load->model('setting/module');

		$modules_setting = $this->model_setting_module->getModulesByCode(str_replace("module_", "", self::MODULE_NAME));

		$api_code = file_get_contents(self::API_TEMP);

		$code = "";

		foreach($modules_setting as $module_setting) {
			$data = json_decode($module_setting['setting'], true);
			if ($data['status'] == 1) {
				$setting = json_decode($module_setting['setting'], true);
				$code = $code . "\n //". $module_setting['name'] . "\n" . 
						html_entity_decode($setting['code'], ENT_QUOTES, "utf-8");
			}
		}

		$api_code = str_replace('#code', $code, $api_code);

		file_put_contents(self::API_FILE, $api_code);
	}

	private function addModule($settings)
	{
		$this->load->model('setting/module');

		$this->model_setting_module->addModule(str_replace("module_", "", self::MODULE_NAME), $settings);

		return $this->db->getLastId();
	}

	protected function editModule($module_id)
	{
		$this->load->model('setting/module');

		/* Set page title */
		$this->load->language('extension/module/quickadmin');
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

			$this->compileAPI();

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->post['name']);

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/quickadmin', 'user_token=' . $this->session->data['user_token'], true)
		);

		$module_setting = $this->model_setting_module->getModule($module_id);

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = $module_setting['name'];
		}
		
		// Status
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else {
			$data['status'] = $module_setting['status'];
		}

		// Code
		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} else {
			$data['code'] = $module_setting['code'];
		}

		$data['action']['save'] = "";
		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['error'] = $this->error;

		// View components
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$htmlOutput = $this->load->view('extension/module/quickadmin', $data);
		$this->response->setOutput($htmlOutput);
	}

	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/quickadmin')) {
			$this->error['permission'] = true;
			return false;
		}

		if (!utf8_strlen($this->request->post['name'])) {
			$this->error['name'] = true;
		}

		if (!utf8_strlen($this->request->post['status'])) {
			$this->error['status'] = true;
		}

		if (!utf8_strlen($this->request->post['code'])) {
			$this->error['code'] = true;
		}

		return empty($this->error);
	}

	public function install()
	{
		$this->load->model('setting/setting');

		$this->model_setting_setting->editSetting(self::MODULE_NAME, ['module_quickadmin_status' => 1]);

		foreach (self::DEFAULT_MODULE_SETTINGS as $module_settings) {
			// Extract code from file code
			$code = file_get_contents($module_settings['code']);
			// Set code in module settings
			$module_settings['code'] = $code;
			// Add Module
			$module_id = $this->addModule($module_settings);
		}

		$this->compileAPI();
	}

	public function uninstall()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_quickadmin');
		unlink(self::API_FILE);
	}
}
