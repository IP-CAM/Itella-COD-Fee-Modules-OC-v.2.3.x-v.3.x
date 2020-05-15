<?php
class ModelExtensionTotalItellaCodFee extends Model
{
	public function getTotal($total)
	{
		$total_min = (float) $this->config->get('itella_cod_fee_total_min');
		$total_max = (float) $this->config->get('itella_cod_fee_total_max');

		$sub_total = (float) $this->cart->getSubTotal();

		$status = true;

		if (!isset($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] != 'itella_cod') {
			$status = false;
		}

		if ($total_min && $total_min > $sub_total) {
			$status = false;
		}

		if ($total_max && $total_max < $sub_total) {
			$status = false;
		}

		if ($sub_total <= 0) {
			$status = false;
		}

		if ($status) {
			$this->load->language('extension/total/itella_cod_fee');

			$fee = (float) $this->config->get('itella_cod_fee_fee_flat');
			$title_postfix = '';
			if ($this->config->get('itella_cod_fee_fee_type')) { // percentage
				$title_postfix = ' ' . (float) $this->config->get('itella_cod_fee_fee_perc') . '%';
				$fee = $sub_total * ((float) $this->config->get('itella_cod_fee_fee_perc') / 100);
			}

			$total['totals'][] = array(
				'code'       => 'itella_cod_fee',
				'title'      => $this->language->get('text_title') . $title_postfix,
				'value'      => $fee,
				'sort_order' => $this->config->get('itella_cod_fee_sort_order')
			);

			if ($this->config->get('itella_cod_fee_tax_class_id')) {
				$tax_rates = $this->tax->getRates($fee, $this->config->get('itella_cod_fee_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total['total'] += $fee;
		}
	}
}
