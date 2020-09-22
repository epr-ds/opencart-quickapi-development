<?php

class ControllerApi%s extends Controller
{
    public function __construct() {
        $this->load->model("user/api");
        $this->load->language('api/quickapi');
	}

    private function validateSession()
    {
        $this->response->addHeader('Content-Type: application/json');

        $api_token = $this->request->get('api_token');
        
        $json = array();

        if (!isset($api_token)){
            $json['error'] = $this->language->get('error_token');
            $this->response->setOutput(json_encode($json));
            return false;
        }

        $api_sessions = $this->mode_user_api->getApiSessions($this->session->data['api_id']);
        $validated = false;
        foreach($api_sessions as $api_session){
            if ($api_token == $api_session['token'] && $this->request->server['REMOTE_ADDR'] == $api_session['ip']){
                $validated = true;
                break;
            }
        }

        if (!$validated) {
            $json['error'] = $this->language->get('error_session');
            $this->response->setOutput(json_encode($json));
        }

        return $validated;
    }

    private function processRequest($requestType = "GET", $callback){
        if (!is_callable($callback))
            throw new Exception("Callback to handle request is not a callable function");
        if ($requestType == 'GET'){
            $callback($this->resquest->get);
        }elseif ($requestType == 'POST'){
            $callback($this->resquest->post);
        }elseif ($requestType == 'PUT'){
            parse_str(file_get_contents("php://input"), $put);
            $callback($put);
        }elseif ($requestType == 'DELETE'){
            $callback($this->resquest->get);
        }else{
            throw new Exception("Requeste Type is not supported");
        }
    }

    %s
}
