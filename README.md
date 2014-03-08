Codeigniter NAB Library
=======================

NAB Library is a library for Codeigniter. It processes payments / transaction using NAB API.


# Installation

Add NAB access details in application/config/config.php
	Required details are:
	- $config['NAB_merchant_id'] - Nab Merchant ID
	- $config['NAB_password'] - Password for the above merchant id.
	- $config['NAB_payment_url'] - NAB API URL

Load library in your controller
	$params = array(
				'NAB_merchant_id'	=>	$this->config->item('NAB_merchant_id'),
				'NAB_password'		=>	$this->config->item('NAB_password'),
				'NAB_payment_url'	=>	$this->config->item('NAB_payment_url'),
			);

	$this->load->library('nab_library', $params);

Get all the information from payment form such as 'credit card number', 'expiry date', 'cvv', 'purchase id/no', 'cost/amount' and save the information into $data as an array and pass it to the function 'pay_now'.
	$this->nab_library->pay_now($data);

*Note: Cost/Amount should be multiplied by 100 before passing it to the library.