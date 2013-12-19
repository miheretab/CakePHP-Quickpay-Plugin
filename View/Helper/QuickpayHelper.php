<?php
/**
 * QuickpayHelper
 *
 * A helper that handles payment processing using Quickpay.
 *
 * PHP version 5
 *
 * @package		QuickpayHelper
 * @author		Miheretab Alemu <mihrtab@gmail.com>
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link		https://github.com/miheretab/CakePHP-Quickpay-Plugin
 */
App::uses('Helper', 'View');

/**
 * Quickpay helper
 *
 */
class QuickpayHelper extends Helper {
/**
 * your Quickpay MD5 secret
 *
 * @var string
 * @access private
 */
	private $secret;
/**
 * your Quickpay merchant ID (quickpay ID)
 *
 * @var int
 * @access private
 */	
	private $merchant;
/**
 * Default Quickpay apikey NULL
 * API key is an alternative to IP based API access control and should be provided
 * if you haven't whitelisted your servers IP in the QuickPay Manager.
 *
 * @var string
 * @access private
 */	
	private $apikey = NULL;
/**
 * Default Quickpay mode to use: Test(1) or Live(0)
 *
 * @var int
 * @access private
 */	
	private $testmode = 1;
/**
 * Default Quickpay API protocol 7
 *
 * @var int
 * @access private
 */		
	private $protocol = 7;	

/**
 * Constructor
 *
 * @param View $View
 * @param array $settings
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		// if testmode is set in bootstrap.php, use it. otherwise, Test(1).
		$testmode = Configure::read('Quickpay.testmode');
		if ($testmode) {
			$this->testmode = $testmode;
		}

		// SET Quickpay merchant ID (quickpay ID)
		$merchant = Configure::read('Quickpay.quickpay_id');
		if ($merchant) {
			$this->merchant = $merchant;
		}	

		// SET Quickpay MD5 secret
		$secret = Configure::read('Quickpay.secret');
		if ($secret) {
			$this->secret = $secret;
		}
		
		// if apikey is set in bootstrap.php, use it. otherwise, NULL.
		$apikey = Configure::read('Quickpay.apikey');
		if ($apikey) {
			$this->apikey = $apikey;
		}
	}
	/**
	* Generate the hidden fields for the QuickPay Payment Window.
	*
	* @param array $input_data The form data to send with the request
	* @param boolean $xhtml Set to TRUE to close tags in XHTML form.
	* @return string The hidden fields in HTML form.
	*/
	public function form_fields($input_data, $xhtml = FALSE)
	{		
		$reserved_fields = array('protocol', 'merchant', 'testmode');
		$valid_input_ordered = array('protocol', 'msgtype', 'merchant', 'language', 'ordernumber', 'amount', 'currency', 'continueurl', 'cancelurl', 'callbackurl', 'autocapture', 'autofee', 'cardtypelock', 'description', 'group', 'testmode', 'splitpayment', 'forcemobile', 'deadline');
		
		foreach($valid_input_ordered as $key)
		{
			// Is the key a reserved field?
			if(in_array($key, $reserved_fields))
			{
				$data_fields[$key] = $this->{$key};
				continue;
			}
			
			if(isset($input_data[$key]))
			{
				$data_fields[$key] = $input_data[$key];
			}			
		}
		
		$html = '';
		$html_end = ($xhtml) ? ' />' : '>';
		$data_fields['md5check'] = md5(implode("", $data_fields) . $this->secret);
		foreach($data_fields as $key => $value)
		{
			$html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"'.$html_end;
		}
		
		return $html;
	}
}
