<?php

/**
 * Name: QuickAPI Developer
 * Version: 1.1.0
 * Author: PerezRE
 * Last Date Updated: Sep 19, 2020
 * Description: API builder for OpenCart v3
 *  */

class ControllerExtensionModuleQuickapi extends Controller
{
	const MODULE_CODE = 'quickapi';
	const MODULE_SETTINGS = [
		'name' 			=> 'Config', 
		'filename' 		=> 'quickapi', 
		'description' 	=> '', 
		'last_filename' => '',
		'del_last' 		=> 1,
		'status'		=> 1
	];

	const API_CORE = DIR_APPLICATION . 'controller/extension/module/quickapi/core.php';
	const API_FILE = DIR_CATALOG . 'controller/api/%s.php';

	private $error = array();

	public function index()
	{
		$this->load->model('setting/module');
		$this->load->model('setting/setting');
		$this->load->language('extension/module/quickapi');

		$this->document->setTitle($this->language->get('heading_title'));

		$module_setting_id = $this->model_setting_setting->getSettingValue('module_quickapi_setting');
		
		$module_id = $this->request->get['module_id'];

		$data = array();

		$data['is_setting_module'] = $module_id == $module_setting_id;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($data['is_setting_module'])) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule(self::MODULE_CODE, $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->buildAPI($module_setting_id);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->post['name']);

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		if (!empty($this->error)){
			$data['error'] = $this->error;
		}

		// Load module settings
		$module_info = $this->model_setting_module->getModule($module_id);

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($module_info)) {
			$data['description'] = $module_info['description'];
		} else {
			$data['description'] = '';
		}

		if ($data['is_setting_module']) {
			// Load data settings
			if (isset($this->request->post['filename'])) {
				$data['filename'] = $this->request->post['filename'];
			} elseif (!empty($module_info)) {
				$data['filename'] = $module_info['filename'];
			} else {
				$data['filename'] = '';
			}
			
			if (isset($this->request->post['del_last'])) {
				$data['del_last'] = $this->request->post['del_last'];
			} elseif (!empty($module_info)) {
				$data['del_last'] = $module_info['del_last'];
			} else {
				$data['del_last'] = 1;
			}
		} else { 
			// Load data for a generic module
			if (isset($this->request->post['status'])) {
				$data['status'] = $this->request->post['status'];
			} elseif (!empty($module_info)) {
				$data['status'] = $module_info['status'];
			} else {
				$data['status'] = true;
			}
			
			if (isset($this->request->post['code'])) {
				$data['code'] = $this->request->post['code'];
			} elseif (!empty($module_info)) {
				$data['code'] = $module_info['code'];
			} else {
				$data['code'] = '';
			}
		}

		// View data
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
			'href' => $this->url->link('extension/module/quickapi', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action']['save'] = "";
		$data['action']['build'] = "";
		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['error'] = $this->error;

		// View components
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$htmlOutput = $this->load->view('extension/module/quickapi', $data);
		$this->response->setOutput($htmlOutput);
	}

	public function install()
	{
		$this->load->model('setting/module');
		$this->load->model('setting/setting');
		$this->load->language('extension/module/quickapi');

		# Create settings module.
		$settings = self::MODULE_SETTINGS;
		$settings['name'] = $this->language->get('settings_module_name');

		$this->model_setting_module->addModule(self::MODULE_CODE, $settings);
		$module_id = $this->db->getLastId();

		# Add settings again
		$this->model_setting_setting->editSetting("module_".self::MODULE_CODE, ['module_quickapi_status' => 1, 'module_quickapi_setting' => $module_id]);
	}

	public function uninstall()
	{
		$this->load->model('setting/module');
		$this->load->model('setting/setting');
		
		# Delete the settings for this extension
		$module_id = $this->model_setting_setting->getSettingValue('module_quickapi_setting');

		$module_info = $this->model_setting_module->getModule($module_id);

		# Delete built file
		$filename = sprintf(self::API_FILE, $module_info['filename']);
		unlink($filename);

		#Delete submodules
		$this->model_setting_module->deleteModulesByCode(self::MODULE_CODE);

		#Delete extension setting
		$this->model_setting_setting->deleteSetting("module_".self::MODULE_CODE);
	}

	protected function sanitizeFilename($filename)
	{
		$filename = explode(" ", $filename);
		$name = "";
		foreach($filename as $str){
			$name .= strtolower($str);
		}
		return $name;
	}

	protected function buildCode($module_setting_id)
	{
		$modules_info = $this->model_setting_module->getModulesByCode(self::MODULE_CODE);
		$code = "";
		foreach($modules_info as $module_info) {
			$settings = json_decode($module_info['setting'], true);
			if ($module_info['module_id'] != $module_setting_id && $settings['status'] == 1) {
				$code .= html_entity_decode($settings['code'], ENT_QUOTES, "utf-8");
			}
		}
		return $code;
	}

	protected function buildAPI($module_setting_id)
	{
		$setting = $this->model_setting_module->getModule($module_setting_id);

		if (isset($setting['last_filename']) && strlen($setting['last_filename']) > 0 && $setting['del_last']){
			unlink($setting['last_filename']);
		}

		$code_core = file_get_contents(self::API_CORE);

		$filename = $this->sanitizeFilename($setting['filename']);

		$filename[0] = strtoupper($filename[0]); // filename -> Filename

		$code = $this->buildCode($module_setting_id);

		$api_code = sprintf($code_core, $filename, $code);

		$filename[0] = strtolower($filename[0]); // Filename -> filename

		$output_file = sprintf(self::API_FILE, $filename);
		
		file_put_contents($output_file, $api_code);

		# Update settings data.
		$setting['last_filename'] = $output_file;
		$setting['name'] = $this->language->get('settings_module_name');
		$this->model_setting_module->editModule($module_setting_id, $setting);
	}

	public function validate($is_settings)
	{
		# $this->load->helper('utf8');
		if (!$this->user->hasPermission('modify', 'extension/module/quickapi')) {
			$this->error['permission'] = true;
			return false;
		}

		if (!utf8_strlen($this->request->post['name'])) {
			$this->error['name'] = true;
		}

		if ($is_settings){
			if(!utf8_strlen($this->request->post['filename'])) {
				$this->error['filename'] = true;
			}
			if(!isset($this->request->post['del_last'])) {
				$this->error['del_last'] = true;
			}
		}else {
			if (!isset($this->request->post['status'])) {
				$this->error['status'] = true;
			}
			
			if (!utf8_strlen($this->request->post['code'])) {
				$this->error['code'] = true;
			}
		}

		return empty($this->error);
	}
}
