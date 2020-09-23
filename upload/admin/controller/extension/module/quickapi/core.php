<?php

class ControllerApi%s extends Controller
{
    private function getApiSessions($api_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE api_id = '" . (int)$api_id . "'");
        return $query->rows;
    }

    private function validateSession()
    {
        $this->response->addHeader('Content-Type: application/json');
        $api_token = $this->request->get['api_token'];

        if (!isset($api_token)){
            return false;
        }

        $api_sessions = $this->getApiSessions($this->session->data['api_id']);
        $validated = false;
        foreach($api_sessions as $api_session){
            if ($api_token == $api_session['session_id']){
                $validated = true;
                break;
            }
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
