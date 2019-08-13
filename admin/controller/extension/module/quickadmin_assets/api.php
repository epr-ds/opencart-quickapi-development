<?php

/**
 * Name: Controller REST API QuickAdmin
 * Version: 1.0.0.0
 * Author: PerezRE
 * Last Date Updated: Jul 16, 2019
 * Description: REST API for OpenCart QuickAdmin WinForms APP
 *  */

class ControllerApiQuickadmin extends Controller
{
    public function login()
    {
        $this->load->language('api/quickadmin');

        $json = array();

        $this->load->model('account/api');

        $data = $this->html_utf_decode($this->request->post['data']);
        
        $data = json_decode($data, true);

        // Login with API Key
        $api_info = $this->model_account_api->login($data['username'], $data['key']);

        if ($api_info) {
            // Check if IP is allowed
            $ip_data = array();

            $results = $this->model_account_api->getApiIps($api_info['api_id']);

            foreach ($results as $result) {
                $ip_data[] = trim($result['ip']);
            }

            if (!in_array($this->request->server['REMOTE_ADDR'], $ip_data)) {
                $json['message'] = sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
            }

            if (!$json) {
                $json['message'] = $this->language->get('login_success');

                $session = new Session($this->config->get('session_engine'), $this->registry);
                $session->start();

                $this->model_account_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);

                // Store cache
                $this->$session->data['api_id'] = $api_info['api_id'];

                // Get Token
                $json['api_token'] = $session->getId();
            } else {
                $json['meesage'] = $this->language->get('error_key');
            }
        } else {
            $json['message'] = $this->language->get('error_permission');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function ValidateSession()
    {

        $this->load->language('api/quickadmin');

        $this->load->model('account/api');

        // Get token
        $api_token = (isset($this->request->post['api_token'])) 
        ? $this->html_utf_decode($this->request->post['api_token'])
        : null;

        if (!isset($api_token) || !isset($this->session->data['api_data']['api_id'])) {
            return $this->language->get('error_permission');
        }

        // Check if IP is allowed
        $ip_data = array();

        $results = $this->model_account_api->getApiIps($this->session->data['api_data']['api_id']);

        foreach ($results as $result) {
            $ip_data[] = trim($result['ip']);
        }

        if (!in_array($this->request->server['REMOTE_ADDR'], $ip_data)) {
            return sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
        }

        // Check API token
        $this->load->model('extension/module/quickadmin');

        $data = array(
            'api_token' => $api_token,
            'api_id'    => $this->session->data['api_data']['api_id']
        );

        $api_session = $this->model_extension_module_quickadmin->getApiSessions($data);

        if ($api_session == 0) {
            return $this->language->get('error_permission');
        }

        return true;
    }

    private function html_utf_decode($text)
    {
        return html_entity_decode($text, ENT_COMPAT, 'UTF-8');
    }

    private function process_img($image)
    {
        $isNew = $image['is_new'];
        $name = $image['name'];
        $files = $this->request->files;
        if ($isNew) {
            $file = $files[$name];
            $target_path = DIR_IMAGE . '/catalog/quickadmin';
            if (!file_exists($target_path))
                mkdir($target_path, 0775, true);
            $img_path = $target_path . '/' . $file['name'];
            if (!file_exists($img_path))
                return (move_uploaded_file($file["tmp_name"], $img_path)) ? "/catalog/quickadmin/" . $file['name'] : "";
            else
                return "/catalog/quickadmin/" . $file['name'];
        } else
            return $image['image'];
    }

    #code
}
