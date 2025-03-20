<?php
class ControllerExtensionPaymentItellaCod extends Controller
{
  private $error = array();
  private $_version = '1.1.2';

  public function install()
  {
    $this->load->model('setting/setting');

    $order_status = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order_status` WHERE `name` = 'Pending' LIMIT 1")->row;

    $this->model_setting_setting->editSetting('itella_cod', array(
      'itella_cod_order_status_id' => (int) $order_status['order_status_id']
    ));

    // copy modification xml
    $this->copyModificationXML();
  }

  public function uninstall()
  {
    $this->removeModificationXML();
  }

  public function index()
  {
    $this->load->language('extension/payment/itella_cod');

    $this->document->setTitle($this->language->get('heading_title'));

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->prepPostData();
      $this->saveSettings($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/payment/itella_cod', $this->getUserToken(), true));
    }

    $data['itella_cod_version'] = $this->_version;

    $data['heading_title'] = $this->language->get('heading_title');

    $data['text_edit'] = $this->language->get('text_edit');
    $data['text_enabled'] = $this->language->get('text_enabled');
    $data['text_disabled'] = $this->language->get('text_disabled');
    $data['text_all_zones'] = $this->language->get('text_all_zones');

    $data['entry_order_status'] = $this->language->get('entry_order_status');
    $data['entry_total'] = $this->language->get('entry_total');
    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
    $data['entry_status'] = $this->language->get('entry_status');
    $data['entry_sort_order'] = $this->language->get('entry_sort_order');
    $data['entry_terms'] = $this->language->get('entry_terms');

    $data['entry_shipping_options'] = $this->language->get('entry_shipping_options');
    $data['text_shipping_options_help'] = $this->language->get('text_shipping_options_help');

    $data['help_total'] = $this->language->get('help_total');
    $data['help_geo_zone'] = $this->language->get('help_geo_zone');

    $data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', $this->getUserToken(), true)
    );

    $extension_home = 'extension';
    if (version_compare(VERSION, '3.0.0', '>=')) {
      $extension_home = 'marketplace';
    }

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link($extension_home . '/extension', $this->getUserToken() . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/payment/itella_cod', $this->getUserToken(), true)
    );

    $data['action'] = $this->url->link('extension/payment/itella_cod', $this->getUserToken(), true);

    $data['cancel'] = $this->url->link($extension_home . '/extension', $this->getUserToken() . '&type=payment', true);

    // settings
    foreach (array('total', 'order_status_id', 'geo_zone_id') as $key) {
      if (isset($this->request->post['itella_cod_' . $key])) {
        $data['itella_cod_' . $key] = $this->request->post['itella_cod_' . $key];
      } else {
        $data['itella_cod_' . $key] = $this->config->get('itella_cod_' . $key);
      }
    }

    // options that needs json_decode
    foreach (array('terms', 'shipping_options') as $key) {
      if (isset($this->request->post['itella_cod_' . $key])) {
        $data['itella_cod_' . $key] = json_decode($this->request->post['itella_cod_' . $key]);
      } else {
        $data['itella_cod_' . $key] = json_decode($this->config->get('itella_cod_' . $key));
      }
    }

    // version specific settings
    $prefix = '';
    if (version_compare(VERSION, '3.0.0', '>=')) {
      $prefix = 'payment_';
    }

    foreach (array('status', 'sort_order') as $key) {
      if (isset($this->request->post[$prefix . 'itella_cod_' . $key])) {
        $data['itella_cod_' . $key] = $this->request->post[$prefix . 'itella_cod_' . $key];
      } else {
        $data['itella_cod_' . $key] = $this->config->get($prefix . 'itella_cod_' . $key);
      }
    }

    // installed shipping modules
    $data['shipping_options'] = $this->loadShippingOptions();

    $this->load->model('localisation/tax_class');

    $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    $this->load->model('localisation/language');

    $data['languages'] = $this->model_localisation_language->getLanguages();

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/itella_cod', $data));
  }

  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/payment/itella_cod')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }

  protected function loadShippingOptions()
  {
    $result = array();

    if (version_compare(VERSION, '3.0.0', '>=')) {
      $this->load->model('setting/extension');
      $shipping_modules = $this->model_setting_extension->getInstalled('shipping');
    } else {
      $this->load->model('extension/extension');
      $shipping_modules = $this->model_extension_extension->getInstalled('shipping');
    }

    foreach ($shipping_modules as $shipping) {
      $this->load->language('extension/shipping/' . $shipping);
      $result[$shipping] = $this->language->get('heading_title');
    }

    return $result;
  }

  private function copyModificationXML()
  {
    $xml_file = DIR_APPLICATION . 'view/image/itella_cod/itella_cod.ocmod.xml';
    if (version_compare(VERSION, '3.0.0', '>=')) {
      $xml_file = DIR_APPLICATION . 'view/image/itella_cod/itella_cod_oc3.ocmod.xml';
    }
    $target = DIR_SYSTEM . 'itella_cod.ocmod.xml';
    if (is_file($target)) {
      unlink($target);
    }

    copy($xml_file, $target);
  }

  private function removeModificationXML()
  {
    $target = DIR_SYSTEM . 'itella_cod.ocmod.xml';
    if (is_file($target)) {
      unlink($target);
    }
  }

  private function prepPostData()
  {
    // json_encode arrays
    if (isset($this->request->post['itella_cod_terms'])) {
      $this->request->post['itella_cod_terms'] = json_encode($this->request->post['itella_cod_terms']);
    } else {
      $this->request->post['itella_cod_terms'] = json_encode(array());
    }

    if (isset($this->request->post['itella_cod_shipping_options'])) {
      $this->request->post['itella_cod_shipping_options'] = json_encode($this->request->post['itella_cod_shipping_options']);
    } else {
      $this->request->post['itella_cod_shipping_options'] = json_encode(array());
    }

    // Opencart 3 expects to start with payment_
    if (version_compare(VERSION, '3.0.0', '>=') && isset($this->request->post['itella_cod_status'])) {
      $this->request->post['payment_itella_cod_status'] = $this->request->post['itella_cod_status'];
      unset($this->request->post['itella_cod_status']);
    }

    // Opencart 3 expects to start with payment_
    if (version_compare(VERSION, '3.0.0', '>=') && isset($this->request->post['itella_cod_sort_order'])) {
      $this->request->post['payment_itella_cod_sort_order'] = $this->request->post['itella_cod_sort_order'];
      unset($this->request->post['itella_cod_sort_order']);
    }
  }

  private function saveSettings($data)
  {
    $this->load->model('setting/setting');

    foreach ($data as $key => $value) {
      $query = $this->db->query("SELECT setting_id FROM `" . DB_PREFIX . "setting` WHERE `code` = 'itella_cod' AND `key` = '" . $this->db->escape($key) . "'");
      if ($query->num_rows) {
        $id = $query->row['setting_id'];
        $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0' WHERE `setting_id` = '$id'");
      } else {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '0', `code` = 'itella_cod', `key` = '$key', `value` = '" . $this->db->escape($value) . "'");
      }
    }
  }

  private function getUserToken()
  {
    if (version_compare(VERSION, '3.0.0', '>=')) {
      return 'user_token=' . $this->session->data['user_token'];
    }
    return 'token=' . $this->session->data['token'];
  }
}
