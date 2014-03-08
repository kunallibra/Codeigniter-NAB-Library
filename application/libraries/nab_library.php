<?php
/**
 *
 *
 * @package    NAB Library
 * @author     Abhishek Salian <abhi@pixelhyperlink.com>
 * @copyright  2014 Sebastian Abhishek Salian <abhi@pixelhyperlink.com>
 * @license    http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @link       http://www.pixelhyperlink.com/
 * @version		Version 1.0
 */
class nab_library {
	private $merchant_id;
	private $password;
	private $payment_url;

	/**
    * Constructor
    * 
    * @access public
    */
  	public function __construct($params)
  	{
  		$this->merchant_id = $params['NAB_merchant_id'];
  		$this->password = $params['NAB_password'];
  		$this->payment_url = $params['NAB_payment_url'];
  	}

  	/** 
    * Encodes string for use in XML 
    * 
    * @access public 
    * @param array
    */
    public function pay_now($data)
    {
    	$nabXml = '<NABTransactMessage>
            <MerchantInfo>
                <merchantID>'.$this->merchant_id.'</merchantID>
                <password>'.$this->password.'</password>
            </MerchantInfo>
            <RequestType>Payment</RequestType>
            <Payment>
                <TxnList count="1">
                    <Txn ID="1">
                        <txnType>0</txnType>
                        <txnSource>23</txnSource>
                        <amount>'.$data['cost'].'</amount>
                        <currency>AUD</currency>
                        <purchaseOrderNo>A'.$data['purchase_id'].'</purchaseOrderNo>
                        <CreditCardInfo>
                            <cardHolderName>test123</cardHolderName>
                            <cardNumber>'.$data['credit_card_no'].'</cardNumber>
                            <cvv>'.$data['security_code'].'</cvv>
                            <expiryDate>'.$data['expiry_month'].'/'.$data['expiry_year'].'</expiryDate>
                        </CreditCardInfo>
                    </Txn>
                </TxnList>
            </Payment>
        </NABTransactMessage>';
        
        $result = $this->makeCurlCall(
            $this->payment_url, /* CURL URL */
            "POST", /* CURL CALL METHOD */
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($nabXml))
            ),
            null, /* CURL GET PARAMETERS */
            $nabXml /* CURL POST PARAMETERS AS XML */
        );

        $this->payment_process($result);
    }

    /** 
    * Connects to NAB API using curl.
    * 
    * @access public 
    * @param string 
    * @return array 
    */
    private function makeCurlCall($url, $method = "GET", $headers = null, $gets = null, $posts = null) {
        $ch = curl_init();
        if($gets != null)
        {
            $url.="?".(http_build_query($gets));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if($posts != null)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
        }
        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if($method == "PUT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        } else if($method == "HEAD") {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }
        if($headers != null && is_array($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);
        return array(
            "code" => $code,
            "response" => $response
        );
    }

    /** 
    * Processes the result obtained from the NAB API.
    * 
    * @access public 
    * @param array
    */
    private function payment_process($result)
    {
        $response = new SimpleXMLElement($result['response']);
        $simple = simplexml_load_string($result['response']);
        $approved = (string) $simple->Payment->TxnList->Txn->approved;
        $responseCode = (string) $simple->Payment->TxnList->Txn->responseCode;
        
        //Display result based on response code found in result array.
        if($approved == 'Yes' && ($responseCode == '00' || $responseCode == '08')){
            echo 'Success: Payment has been successfully made.';
        } else {
            if($responseCode == '101') {
                echo 'Error: Invalid Credit Card Number - Payment was not processed';
            } else if($responseCode == '109') {
                echo 'Error: Invalid CVV2/CVC2 - Payment was not processed';
            } else if($responseCode == '51') {
                echo 'Error: Insufficient Funds - Payment was not processed';
            } else {
                echo 'Error: Unkown Error - Payment was not processed';
            }
        }
    }
}

/* End of file nab_library.php */
/* Location: ./application/libraries/nab_library.php */