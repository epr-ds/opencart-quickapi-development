public function getPaymentMethods()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {

        $this->load->model('extension/module/quickadmin');
        // Load extensions
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('payment');

        // Payment data required for grabbing payment_methods.
        $payment_address = array('country_id' => 138, 'zone_id' => 0);
        $total = $this->config->get('payment_cod_total');

        // Get dummy products to get payment methods
        $products = $this->model_extension_module_quickadmin->getProducts();
        foreach ($products as $product)
            $this->cart->add($product['product_id'], 1, null);

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $this->load->model('extension/payment/' . $result['code']);

                $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($payment_address, $total);

                if ($method) {
                    $json[] = array(
                        'code'          => $method['code'],
                        'title'         => strip_tags($method['title']),
                        'terms'         => $method['terms'],
                        'sort_order'    => $method['sort_order']
                    );
                }
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getShippingMethods()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load extensions
        $this->load->model('setting/extension');
        // Shipping methods
        $results = $this->model_setting_extension->getExtensions('shipping');

        // Shipping data required for grabbing shipping_methods
        $shipping_address = array('country_id' => 138, 'zone_id' => 2158); // MX, JAL.
        foreach ($results as $result) {
            if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                $this->load->model('extension/shipping/' . $result['code']);

                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($shipping_address);

                if ($quote) {
                    $shipping_methods[] = array(
                        'title'      => html_entity_decode($quote['title']),
                        'quote'      => $quote['quote'],
                    );
                }
            }
        }

        if (count($shipping_methods) > 0) {
            foreach ($shipping_methods as $shipping_method) {
                foreach ($shipping_method as $key => $quotes) {
                    foreach ($quotes as $key => $quote) {
                        $json[] = array(
                            'title'         => html_entity_decode($quote['title']),
                            'code'          => $quote['code'],
                            'cost'          => $quote['cost'],
                            'tax_class_id'  => $quote['tax_class_id'],
                            'text'          => $quote['text'],
                        );
                    }
                }
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getLanguages()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getLanguages();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCustomerGroups()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getCustomerGroups();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getManufacturers()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getManufacturers();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCurrencies()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $currencies = $this->model_extension_module_quickadmin->getCurrencies();

        foreach ($currencies as $code => $currency) {
            $json[] = $currency;
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getZones()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        //$country_id = 138; ID for Mexico.
        if (isset($this->request->post['data'])) {
            // Load quickadmin model.
            $this->load->model('extension/module/quickadmin');
            $json_data = stripslashes($this->html_utf_decode($this->request->post['data']));
            $data = json_decode($json_data, true);
            $country_id = $data['country_id'];
            if ($country_id) {
                $json = $this->model_extension_module_quickadmin->getZonesByCountryId($country_id);
            }
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCountries()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getCountries();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getWeightClasses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getWeightClasses();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getLengthClasses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getLengthClasses();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getCategories()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $categories = $this->model_extension_module_quickadmin->getCategories();

        foreach ($categories as $category) {
            $json[] = array(
                "category_id"   => $category['category_id'],
                "sort_order"    => $category['sort_order'],
                "status"        => $category['status'],
                "name"          => html_entity_decode($category['name']),
                "store_id"      => $category['store_id'],
            );
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getTaxClasses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getTaxClasses();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getStockStatuses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getStockStatuses();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getOrderStatuses()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getOrderStatuses();
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getStores()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        // Load quickadmin model.
        $this->load->model('extension/module/quickadmin');
        $json = $this->model_extension_module_quickadmin->getStores();
        array_push($json, array(
            "store_id" => 0,
            "name" => "Default",
            "url" => HTTPS_SERVER,
            "ssl" => ""
        ));
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getServerData()
{
    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        $json['url_image'] = HTTPS_SERVER . 'image/';
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}