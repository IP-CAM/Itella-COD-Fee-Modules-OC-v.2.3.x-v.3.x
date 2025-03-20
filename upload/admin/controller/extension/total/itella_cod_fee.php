<?php
class ControllerExtensionTotalItellaCodFee extends Controller
{
	private $error = array();
	private $_version = '1.1.2';

	public function index()
	{
		$this->load->language('extension/total/itella_cod_fee');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->prepPostData();
			$this->saveSettings($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total/itella_cod_fee', $this->getUserToken(), true));
		}

		$data['itella_cod_fee_version'] = $this->_version;

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_fee_flat'] = $this->language->get('text_fee_flat');
		$data['text_fee_perc'] = $this->language->get('text_fee_perc');

		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_total_min'] = $this->language->get('entry_total_min');
		$data['entry_total_max'] = $this->language->get('entry_total_max');
		$data['entry_fee_type'] = $this->language->get('entry_fee_type');
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['entry_fee_flat'] = $this->language->get('entry_fee_flat');
		$data['entry_fee_perc'] = $this->language->get('entry_fee_perc');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

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
			'href' => $this->url->link($extension_home . '/extension', $this->getUserToken() . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/itella_cod_fee', $this->getUserToken(), true)
		);

		$data['action'] = $this->url->link('extension/total/itella_cod_fee', $this->getUserToken(), true);

		$data['cancel'] = $this->url->link($extension_home . '/extension', $this->getUserToken() . '&type=total', true);

		// settings
		foreach (array(
			'total', 'tax_class_id',
			'total_min', 'total_max',
			'fee_type', 'fee_flat', 'fee_perc'
		) as $key) {
			if (isset($this->request->post['itella_cod_fee_' . $key])) {
				$data['itella_cod_fee_' . $key] = $this->request->post['itella_cod_fee_' . $key];
			} else {
				$data['itella_cod_fee_' . $key] = $this->config->get('itella_cod_fee_' . $key);
			}
		}

		// version specific settings
		$prefix = '';
		if (version_compare(VERSION, '3.0.0', '>=')) {
			$prefix = 'total_';
		}
		foreach (array('status', 'sort_order') as $key) {
			if (isset($this->request->post[$prefix . 'itella_cod_fee_' . $key])) {
				$data['itella_cod_fee_' . $key] = $this->request->post[$prefix . 'itella_cod_fee_' . $key];
			} else {
				$data['itella_cod_fee_' . $key] = $this->config->get($prefix . 'itella_cod_fee_' . $key);
			}
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/itella_cod_fee', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/total/itella_cod_fee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function prepPostData()
	{
		// Opencart 3 expects to start with total_
		if (version_compare(VERSION, '3.0.0', '>=') && isset($this->request->post['itella_cod_fee_status'])) {
			$this->request->post['total_itella_cod_fee_status'] = $this->request->post['itella_cod_fee_status'];
			unset($this->request->post['itella_cod_fee_status']);
		}

		// Opencart 3 expects to start with total_
		if (version_compare(VERSION, '3.0.0', '>=') && isset($this->request->post['itella_cod_fee_sort_order'])) {
			$this->request->post['total_itella_cod_fee_sort_order'] = $this->request->post['itella_cod_fee_sort_order'];
			unset($this->request->post['itella_cod_fee_sort_order']);
		}
	}

	private function saveSettings($data)
	{
		$this->load->model('setting/setting');

		foreach ($data as $key => $value) {
			$query = $this->db->query("SELECT setting_id FROM `" . DB_PREFIX . "setting` WHERE `code` = 'itella_cod_fee' AND `key` = '" . $this->db->escape($key) . "'");
			if ($query->num_rows) {
				$id = $query->row['setting_id'];
				$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0' WHERE `setting_id` = '$id'");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '0', `code` = 'itella_cod_fee', `key` = '$key', `value` = '" . $this->db->escape($value) . "'");
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
