<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay_now extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/pay_now
	 *	- or -  
	 * 		http://example.com/index.php/pay_now/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/pay_now/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library('form_validation');
		$config = array(
                array(
                    'field'   => 'credit_card_no', 
                    'label'   => 'Credit Card Number', 
                    'rules'   => 'required'
                ),
                array(
                    'field'   => 'expiry_month', 
                    'label'   => 'Expiry Month', 
                    'rules'   => 'required'
                ),
                array(
                    'field'   => 'expiry_year', 
                    'label'   => 'Expiry Year', 
                    'rules'   => 'required'
                ),
                array(
                    'field'   => 'security_code', 
                    'label'   => 'CVV', 
                    'rules'   => 'required'
                ),
                array(
                    'field'   => 'purchase_id', 
                    'label'   => 'Purchase Id/Number', 
                    'rules'   => 'required'
                ),
                array(
                    'field'   => 'cost', 
                    'label'   => 'Cost/Amount', 
                    'rules'   => 'required'
                ),
            );

		$this->form_validation->set_rules($config);

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('pay_now');
		} else {
			$params = array(
				'NAB_merchant_id'	=>	$this->config->item('NAB_merchant_id'),
				'NAB_password'		=>	$this->config->item('NAB_password'),
				'NAB_payment_url'	=>	$this->config->item('NAB_payment_url'),
			);

			$this->load->library('nab_library', $params);

			$data['credit_card_no'] = $this->input->post('credit_card_no');
			$data['expiry_month'] = $this->input->post('expiry_month');
			$data['expiry_year'] = $this->input->post('expiry_year');
			$data['security_code'] = $this->input->post('security_code');
			$data['cost'] = $this->input->post('cost') * 100;
			$data['purchase_id'] = $this->input->post('purchase_id');

			$this->nab_library->pay_now($data);
		}
	}
}

/* End of file pay_now.php */
/* Location: ./application/controllers/pay_now.php */