<?php

class ControllerApi%s extends Controller
{
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

    private function processRequest($handlers){
        $this->response->addHeader('Content-Type: application/json');
        $method = $this->request->server['REQUEST_METHOD'];
        if (!is_callable($handlers[$method]))
            throw new Exception("Callback to handle request is not a callable function");
        if ($method == 'GET'){
            $handlers[$method]($this->request->get);
        }elseif ($method == 'POST'){
            $handlers[$method]($this->request->post);
        }elseif ($method == 'PUT'){
            $handlers[$method]($this->request->get);
        }elseif ($method == 'DELETE'){
            $handlers[$method]($this->request->get);
        }else{
            throw new Exception("Requeste Type is not supported");
        }
    }

    %s
}
