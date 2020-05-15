<?php
class ControllerExtensionPaymentItellaCod extends Controller
{
	public function index()
	{
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/itella_cod', $data);
	}

	public function confirm()
	{
		$json = array();

		if ($this->session->data['payment_method']['code'] == 'itella_cod') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('itella_cod_order_status_id'));

			$json['redirect'] = $this->url->link('checkout/success'); // for OC3
		}

		// OC3 handles it using ajax
		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}
