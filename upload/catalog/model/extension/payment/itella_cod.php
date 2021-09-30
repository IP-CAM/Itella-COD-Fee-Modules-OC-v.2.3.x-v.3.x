<?php
class ModelExtensionPaymentItellaCOD extends Model
{
  public function getMethod($address, $total)
  {
    $this->load->language('extension/payment/itella_cod');

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('itella_cod_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

    $status = true;

    $shipping_methods = json_decode($this->config->get('itella_cod_shipping_options'));

    if (!$shipping_methods) {
      $shipping_methods = array();
    }

    if (isset($this->session->data['shipping_method'])) {
      $method = $this->getShippingMethodModule($this->session->data['shipping_method']);
      // if we have set shipping methods check if selected is one of them
      if ($shipping_methods && !in_array($method, $shipping_methods)) {
        $status = false;
      }
    }

    if ($this->config->get('itella_cod_total') > 0 && $this->config->get('itella_cod_total') > $total) {
      $status = false;
    }

    if (!$this->cart->hasShipping()) {
      $status = false;
    }

    if ($this->config->get('itella_cod_geo_zone_id') && !$query->num_rows) {
      $status = false;
    }

    $method_data = array();

    $terms_options = json_decode($this->config->get('itella_cod_terms'), true);

    $terms = '';
    if ($terms_options && isset($terms_options[$this->config->get('config_language_id')])) {
      $terms = $terms_options[$this->config->get('config_language_id')];
    }

    if ($status) {
      $method_data = array(
        'code'       => 'itella_cod',
        'title'      => $this->language->get('text_title'),
        'terms'      => $terms,
        'sort_order' => $this->config->get('itella_cod_sort_order')
      );
    }

    return $method_data;
  }

  private function getShippingMethodModule($shipping_method)
  {
    if (!isset($shipping_method['code'])) {
      return '';
    }

    return explode('.', $shipping_method['code'])[0];
  }

  private function getShippingMethodName($shipping_method)
  {
    if (!isset($shipping_method['code'])) {
      return '';
    }

    return explode('.', $shipping_method['code'])[1];
  }
}
