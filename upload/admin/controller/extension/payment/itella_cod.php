<?php
class ControllerExtensionPaymentItellaCod extends Controller
{
  private $error = array();
  private $_version = '1.0.0';

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

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('itella_cod', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
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

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/payment/itella_cod', 'token=' . $this->session->data['token'], true)
    );

    $data['action'] = $this->url->link('extension/payment/itella_cod', 'token=' . $this->session->data['token'], true);

    $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

    foreach (array(
      'total', 'order_status_id', 'geo_zone_id', 'status', 'sort_order', 'terms',
      'shipping_options'
    ) as $key) {
      if (isset($this->request->post['itella_cod_' . $key])) {
        $data['itella_cod_' . $key] = $this->request->post['itella_cod_' . $key];
      } else {
        $data['itella_cod_' . $key] = $this->config->get('itella_cod_' . $key);
      }
    }

    $data['shipping_options'] = $this->loadShippingOptions();

    if (!$data['itella_cod_shipping_options']) {
      $data['itella_cod_shipping_options'] = array();
    }

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

    $this->load->model('extension/extension');
    $shipping_modules = $this->model_extension_extension->getInstalled('shipping');
    foreach ($shipping_modules as $shipping) {
      $this->load->language('extension/shipping/' . $shipping);
      $result[$shipping] = $this->language->get('heading_title');
    }

    return $result;
  }

  private function copyModificationXML()
  {
    $xml_file = DIR_APPLICATION . 'view/image/itella_cod/itella_cod.ocmod.xml';
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
}
