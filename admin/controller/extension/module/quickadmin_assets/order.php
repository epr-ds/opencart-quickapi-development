public function addOrder()
{
    $this->load->language('api/quickadmin');
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {
            $json_data = $this->html_utf_decode($this->request->post['data']);
            $order_data = json_decode($json_data, true);
            $customer = $order_data['customer'];
            $payment_addr = $order_data['payment_address'];
            $payment_m = $order_data['payment_method'];
            $shipp_m = $order_data['shipping_method'];
            if (// Validating customer data...
                (int)$customer['customer_group_id'] >= 0
                && strlen($customer['firstname']) > 0
                && strlen($customer['lastname']) > 0
                && strlen($customer['email']) > 0
                && strlen($customer['telephone']) > 0
                // Validating payment data...
                && strlen($payment_m['code']) > 0
                // Validating shipping method...
                && strlen($shipp_m['code']) > 0
                // Validating order data...
                && (float) $order_data['total'] > 0
            ) {
                $this->load->model('extension/module/quickadmin');
                $order_id = $this->model_extension_module_quickadmin->addOrder($order_data);
                $json['message'] = sprintf($this->language->get('order_added'), $order_id);
            } else {
                $json['message'] = $this->language->get('error_order');
            }
        } else {
            $json['message'] = $this->language->get('error_order');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function updateOrder()
{
    $this->load->language('api/quickadmin');
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {
            $json_data = $this->html_utf_decode($this->request->post['data']);
            $order_data = json_decode($json_data, true);
            $customer = $order_data['customer'];
            $payment_addr = $order_data['payment_address'];
            $payment_m = $order_data['payment_method'];
            $shipp_m = $order_data['shipping_method'];
            if (// Validating customer data...
                (int) $customer['customer_group_id'] >= 0
                && strlen($customer['firstname']) > 0
                && strlen($customer['lastname']) > 0
                && strlen($customer['email']) > 0
                && strlen($customer['telephone']) > 0
                // Validating payment data..
                && strlen($payment_m['code']) > 0
                // Validating shipping method...
                && strlen($shipp_m['code']) > 0
                // Validating order total...
                && (float) $order_data['total'] > 0
            ) {
                $this->load->model('extension/module/quickadmin');
                $order_id = $order_data['order_id'];
                $order = $this->model_extension_module_quickadmin->editOrder($order_id, $order_data);
                $json['message'] = sprintf($this->langauge->get('order_updated'), $order_id);
            } else {
                $json['message'] = $this->language->get('error_order');
            }
        } else {
            $json['message'] = $this->language->get('error_order');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getOrders()
{
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        $json_data = $this->html_utf_decode($this->request->post['data']);
        $params = json_decode($json_data, true);
        $params['sort'] = 'o.order_id';
        $params['order'] = 'DESC';
        $this->load->model('extension/module/quickadmin');
        //$total = $this->model_extension_module_quickadmin->getTotalOrders();
        $orders = $this->model_extension_module_quickadmin->getOrders($params);
        $json = $orders;
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getOrder()
{
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {
            $json_data = $this->html_utf_decode($this->request->post['data']);
            $order_data = json_decode($json_data, true);
            if ($order_data['order_id']) {
                $this->load->model('extension/module/quickadmin');
                $order = $this->model_extension_module_quickadmin->getOrder($order_data['order_id']);
                $json = array(
                    'order_id'              => $order['order_id'],
                    'invoice_no'            => $order['invoice_no'],
                    'invoice_prefix'        => $order['invoice_prefix'],
                    'store'                 => array(
                        'store_id'              => $order['store_id'],
                        'name'                  => $order['store_name'],
                        'url'                   => $order['store_url']
                    ),
                    'customer'              => array(
                        'customer_id'           => $order['customer_id'],
                        'customer_group_id'     => $order['customer_group_id'],
                        'firstname'             => $order['firstname'],
                        'lastname'              => $order['lastname'],
                        'email'                 => $order['email'],
                        'telephone'             => $order['telephone']
                    ),
                    'payment_custom_field'      => count($order['payment_custom_field']) > 0
                        ? $order['payment_custom_field'] : array(
                            'invoice_mode' => '',
                            'discount' => '0'
                        ),
                    'payment_address'       => array(
                        'firstname'             => $order['payment_firstname'],
                        'lastname'              => $order['payment_lastname'],
                        'company'               => $order['payment_company'],
                        'address_1'             => $order['payment_address_1'],
                        'address_2'             => $order['payment_address_2'],
                        'postcode'              => $order['payment_postcode'],
                        'city'                  => $order['payment_city'],
                        'zone_id'               => $order['payment_zone_id'],
                        'country_id'            => $order['payment_country_id'],
                    ),
                    'payment_method'        => array(
                        'code'                  => $order['payment_code'],
                        'title'                 => $order['payment_method']
                    ),
                    'shipping_address'       => array(
                        'firstname'             => $order['shipping_firstname'],
                        'lastname'              => $order['shipping_lastname'],
                        'company'               => $order['shipping_company'],
                        'address_1'             => $order['shipping_address_1'],
                        'address_2'             => $order['shipping_address_2'],
                        'postcode'              => $order['shipping_postcode'],
                        'city'                  => $order['shipping_city'],
                        'zone_id'               => $order['shipping_zone_id'],
                        'country_id'            => $order['shipping_country_id'],
                    ),
                    'shipping_method'       => array(
                        'code'                  => $order['shipping_code'],
                        'title'                 => $order['shipping_method']
                    ),
                    'comment'               => $order['comment'],
                    'total'                 => $order['total'],
                    'reward'                => $order['reward'],
                    'order_status_id'       =>  $order['order_status_id'],
                    'commission'            => $order['commission'],
                    'language_id'           => $order['language_id'],
                    'currency'              => array(
                        'currency_id'           => $order['currency_id'],
                        'code'                  => $order['currency_code'],
                        'value'                 => $order['currency_value']
                    ),
                    'ip'                    => $order['ip'],
                    'forwarded_ip'          => $order['forwarded_ip'],
                    'user_agent'            => $order['user_agent'],
                    'accept_language'       => $order['accept_language'],
                    'date_added'            => $order['date_added'],
                    'date_modified'         => $order['date_modified'],
                );

                if (isset($order)) {
                    $order_total = $this->model_extension_module_quickadmin->getOrderTotals($order_data['order_id']);
                    $order_products = $this->model_extension_module_quickadmin->getOrderProducts($order_data['order_id']);

                    $json['order_total'] = $order_total;
                    $json['order_products'] = $order_products;
                }
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function deleteOrder()
{
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {
            $json_data = $this->html_utf_decode($this->request->post['data']);
            $order_data = json_decode($json_data, true);
            $id = $order_data['order_id'];
            if ($id > 0) {
                $this->load->model('extension/module/quickadmin');
                $order = $this->model_extension_module_quickadmin->deleteOrder($id);
                $json['message'] = sprintf($this->language->get('order_deleted'), $id);
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}