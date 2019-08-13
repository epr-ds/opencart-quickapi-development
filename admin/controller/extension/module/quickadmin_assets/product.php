public function addProduct()
{
    $this->load->language('api/quickadmin');

    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {
            $product = $this->html_utf_decode($this->request->post['data']);
            $product = json_decode($product, true);

            // Validate incoming data.
            if (strlen($product['model']) > 0) {
                if (count($product['product_description']) > 0) {
                    $ok = true;
                    foreach ($product['product_description'] as $language_id => $product_description) {
                        if (strlen($product_description['name']) == 0 || strlen($product_description['meta_title']) == 0) {
                            $json['message'] = $this->language->get('error_product');
                            $ok = false;
                            break;
                        }
                    }

                    // Inserting data.
                    if ($ok) {
                        /// cover
                        $product['image'] = $this->process_img($product['image']);
                        /// Subimages
                        $product_images = array();
                        foreach ($product['product_image'] as $image)
                            $product_images[] = array('image' => $this->process_img($image));
                        // Replace from "objs image" to simple path_image.
                        $product['product_image'] = $product_images;
                        // Load product model to perform actions.
                        $this->load->model('extension/module/quickadmin');
                        // Add
                        $product_id = $this->model_extension_module_quickadmin->addProduct($product);
                        $json['message'] = sprintf($this->language->get('product_added'), $product_id);
                    }
                } else {
                    $json['message'] = $this->language->get('error_product');
                }
            } else {
                $json['message'] = $this->language->get('error_product');
            }
        } else {
            $json['message'] = $this->language->get('error_product');
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function updateProduct()
{
    $this->load->language('api/quickadmin');

    $json = array();

    $result = $this->ValidateSession();

    if ($result === true) {
        if (isset($this->request->post['data'])) {

            $product = $this->html_utf_decode($this->request->post['data']);
            $product = json_decode($product, true);
            $product_id = (int) $product['product_id'];

            // Validate incoming data.
            if (
                $product_id
                && strlen($product['model']) > 0
                && count($product['product_description']) > 0
            ) {
                $ok = true;
                foreach ($product['product_description'] as $language_id => $product_description) {
                    if (strlen($product_description['name']) == 0 || strlen($product_description['meta_title']) == 0) {
                        $json['message'] = $this->language->get('error_product');
                        $ok = false;
                        break;
                    }
                }

                // Inserting data.
                if ($ok) {
                    /// cover
                    $product['image'] = $this->process_img($product['image']);
                    /// Subimages
                    $product_images = array();
                    foreach ($product['product_image'] as $image)
                        $product_images[] = array('image' => $this->process_img($image));
                    // Replace from "objs image" to simple path_image.
                    $product['product_image'] = $product_images;
                    // Load product model to perform actions.
                    $this->load->model('extension/module/quickadmin');
                    // Add
                    $this->model_extension_module_quickadmin->updateProduct($product_id, $product);
                    // Message.
                    $json['message'] = sprintf($this->language->get('product_updated'), $product_id);
                } else {
                    $json['message'] = sprintf($this->language->get('error_product'), $product_id);
                }
            } else {
                $json['message'] = sprintf($this->language->get('error_product'), $product_id);
            }
        } else {
            $json['message'] = sprintf($this->language->get('error_product'), $product_id);
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getProducts()
{
    $this->load->language('api/quickadmin');
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->model('extension/module/quickadmin');
        // Params may have "start" and "limit"
        $params_json = $this->html_utf_decode($this->request->post['data']);
        $params = json_decode($params_json, true);
        //$total = $this->model_extension_module_quickadmin->getTotalProducts();
        //$json['total'] = $total;
        $products = $this->model_extension_module_quickadmin->getProducts($params);
        foreach ($products as $product) {
            $json[] = array(
                'product_id' => $product['product_id'],
                'image'      => HTTPS_SERVER . 'image/' . $product['image'],
                'name'       => html_entity_decode($product['name']),
                'model'      => $product['model'],
                'price'    => $product['price'],
                //'price'      => $this->currency->format($product['price'], $this->config->get('config_currency')),
                'quantity'   => $product['quantity'],
                'status'     => $product['status']
            );
        }
    } else {
        $json['message'] = $this->language->get('error_permission');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function getProduct()
{
    $this->load->language('api/quickadmin');
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->model('extension/module/quickadmin');
        $product_json = $this->html_utf_decode($this->request->post['data']);
        $data = json_decode($product_json, true);
        $product_id = $data['product_id'];

        if ($product_id > 0) {
            $product = $this->model_extension_module_quickadmin->getProduct($product_id);
            $product['image'] = array('image' => $product['image']);
            if (isset($product) && count($product) > 0) {
                $product['product_description'] = $this->model_extension_module_quickadmin->getProductDescriptions($product_id);
                $product['product_category'] = $this->model_extension_module_quickadmin->getProductCategories($product_id);
                $product['product_image'] = array();
                $product_images = $this->model_extension_module_quickadmin->getProductImages($product_id);
                foreach ($product_images as $product_image)
                    $product['product_image'][] = array('image' => $product_image['image']);
                $product['product_store'] = $this->model_extension_module_quickadmin->getProductStores($product_id);
                $product['product_layout'] = $this->model_extension_module_quickadmin->getProductLayouts($product_id);
                $product['product_related'] = $this->model_extension_module_quickadmin->getProductRelated($product_id);
                $product['product_discount'] = $this->model_extension_module_quickadmin->getProductDiscounts($product_id);
                $product['product_special'] = $this->model_extension_module_quickadmin->getProductSpecials($product_id);
            }
            $json = $product;
        } else {
            $json['product'] = null;
        }
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function deleteProduct()
{
    $this->load->language('api/quickadmin');
    $json = array();
    $result = $this->ValidateSession();

    if ($result === true) {
        $this->load->model('extension/module/quickadmin');
        $product_json = $this->html_utf_decode($this->request->post['data']);
        $product = json_decode($product_json, true);
        $product_id = $product['product_id'];
        $product = $this->model_extension_module_quickadmin->deleteProduct($product_id);
        $json['message'] = sprintf($this->language->get('product_deleted'), $product_id);
    } else {
        $json['message'] = $result;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}