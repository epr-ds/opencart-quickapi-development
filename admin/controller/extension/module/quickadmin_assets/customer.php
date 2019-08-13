public function addCustomer()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        $data = $this->html_utf_decode($this->request->post['data']);
        // Validate incoming data.
        $data = json_decode($data, true);
        if (
            !isset($data['firstname']) ||
            !isset($data['lastname']) ||
            !isset($data['email']) ||
            !isset($data['telephone']) ||
            !isset($data['password'])
        ) {
            // Load customer model.
            $this->load->model('extension/module/quickadmin');
            $customer_id = $this->model_extension_module_quickadmin->addCustomer($data);
            $json['message'] = sprintf($this->language->get('customer_added'), $customer_id);
        } else {
            $this->load->language('api/quickadmin');
            $json['message'] = $this->language->get('error_customer');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCustomers()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->model('extension/module/quickadmin');
        $params = json_decode($this->html_utf_decode($this->request->post['data']), true);
        $customers = $this->model_extension_module_quickadmin->getCustomers($params);
        foreach ($customers as $customer) {
            $json[] = array(
                'customer_id'    => $customer['customer_id'],
                'firstname'      => explode(' ', $customer['name'])[0],
                'lastname'       => explode(' ', $customer['name'])[1],
                'email'          => $customer['email'],
                'telephone'      => $customer['telephone'],
                'status'         => $customer['status'],
                'address_id'     => $customer['address_id'],
                'date_added'     => date('d/m/Y', strtotime($customer['date_added'])),
            );
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCustomer()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {

        if (isset($this->request->post['data'])) {

            $json_post_data = $this->html_utf_decode($this->request->post['data']);
            $customer_data = json_decode($json_post_data, true);
            $this->load->model('extension/module/quickadmin');
            $customer = $this->model_extension_module_quickadmin->getCustomer($customer_data['customer_id']);

            if ($customer) {
                $json = array(
                    'customer_id'       => $customer['customer_id'],
                    'firstname'         => $customer['firstname'],
                    'lastname'          => $customer['lastname'],
                    'email'             => $customer['email'],
                    'telephone'         => $customer['telephone'],
                    'status'            => $customer['status'],
                    'address_id'        => $customer['address_id'],
                    'customer_group_id' => $customer['customer_group_id'],
                    'newsletter'        => $customer['newsletter'],
                    'safe'              => $customer['safe'],
                    'date_added'        => date('d/m/Y', strtotime($customer['date_added'])),
                );

                $json['address']  = $this->model_extension_module_quickadmin->getAddress($customer['address_id']);
            }
        } else {
            $this->load->language('api/quickadmin');
            $json['message'] = $this->language->get('error_customer');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function updateCustomer()
{
    $json = array();

    $result = $this->ValidateSession();

    $this->load->language('api/quickadmin');

    if ($result === true) {
        // Grab customer data from POST request.
        $customer_data = $this->request->post['data'];

        if (isset($customer_data)) {
            // Remove special characters.
            $customer_json = $this->html_utf_decode($customer_data);
            // decode JSON
            $customer = json_decode($customer_json, true);
            // Validate incoming data. Those fields that are mandatory.
            if ($customer['customer_id'] == 0 
                || strlen($customer['firstname']) == 0 
                || strlen($customer['lastname']) == 0 
                || strlen($customer['email']) == 0 
                || strlen($customer['telephone']) == 0
            ){
                $this->load->model('extension/module/quickadmin');
                $this->model_extension_module_quickadmin->updateCustomer($customer['customer_id'], $customer);
                sprintf($this->language->get('customer_updated'), $customer['customer_id']);
            } else {
                $json['message'] = $this->language->get('error_customer');
            }
        } else {
            $json['message'] = $this->language->get('error_customer');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function deleteCustomer()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->language('api/quickadmin');
        if (isset($this->request->post['data'])) {
            $json_data = stripslashes($this->html_utf_decode($this->request->post['data']));
            $customer_data = json_decode($json_data, true);
            if ($customer_data['customer_id']) {
                $id = $customer_data['customer_id'];
                $this->load->model('extension/module/quickadmin');
                $result = $this->model_extension_module_quickadmin->deleteCustomer($id);
                $json['message'] = sprintf($this->language->get('customer_deleted'), $id);
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getAddresses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->model('extension/module/quickadmin');
        $post = $this->html_utf_decode($this->request->post['data']);
        $data = json_decode($post, true);
        $customer_id = $data['customer_id'];
        $json = $this->model_extension_module_quickadmin->getAddresses($customer_id);
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}