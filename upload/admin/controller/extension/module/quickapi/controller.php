<?php

class ControllerApi%s extends Controller
{
    protected function processRequest($handlers)
    {
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
