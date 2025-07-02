<?php 

require_once 'moneyspace/Config.php';

class Api extends Config
{

	protected static $baseUrl = 'https://a.moneyspace.net/payment';

	protected static $secret_id = null;

	protected static $secret_key = null;

	/**
     * @param string $secret_id
     * @param string $secret_key
     */

	public function __construct($secret_id = null, $secret_key = null)
	{
		self::$secret_id = !empty($secret_id) ? $secret_id : $this->getSecret_id();
		self::$secret_key = !empty($secret_key) ? $secret_key : $this->getSecret_key();
	}

	public function setBaseUrl($baseUrl)
	{
	 	self::$baseUrl = $baseUrl;
	}

	public static function getBaseUrl()
	{
	 	return self::$baseUrl;
	}

	public static function getSecretID()
	{
	 	return self::$secret_id;
	}

	public static function getSecretKey()
	{
	 	return self::$secret_key;
	}

	public function getHash($data,$key) {
		return hash_hmac('sha256', $data, $key);
	}

	public function getTime() {
		return date("YmdHis");
	}

	 public function CallAPI($path,$param) {

	 	$ch = curl_init();
	 	curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl().$path);
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 	curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	 	$data = $param;

	 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	 	$output = curl_exec($ch);
	 	$info = curl_getinfo($ch);
	 	curl_close($ch);

	 	return $output;


	}

	public function CreatePayment($data){

		$msdata =  array_merge($data, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));

	 	$call = $this->CallAPI("/CreateTransaction/",$msdata);

	 	return $call;


	}

	public function Createinstallment($data){


		$msdata =  array_merge($data, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));

		$call = $this->CallAPI("/Createinstallment/",$msdata);

		return $call;


	}

	public function Check_Transaction($tranID){

		$msdata =  array_merge($tranID, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));

	 	$call = $this->CallAPI("/transactionID/",$msdata);

	 	return $call;


	}

	 public function OpenPayment($tranID){

		$msdata =  array_merge($tranID, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));
		
	 	$call = $this->CallAPI("/OpenPayment/",$msdata);

	 	return $call;


	}

	 public function Check_OrderID($orderID){

		$msdata =  array_merge($orderID, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));

	 	$call = $this->CallAPI("/OrderID/",$msdata);

	 	return $call;


	}

	public function Cancel_Payment($tranID){

		$msdata =  array_merge($tranID, array('secret_id' => $this->getSecretID(),'secret_key' => $this->getSecretKey()));

		$ch = curl_init();
	 	curl_setopt($ch, CURLOPT_URL, 'https://a.moneyspace.net/merchantapi/cancelpayment');
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 	curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	 	$data = $msdata;

	 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	 	$output = curl_exec($ch);
	 	$info = curl_getinfo($ch);
	 	curl_close($ch);

	 	return $output;


	}

	public function GetWebhook(){

		$status = $_POST["status"];
		$amount = $_POST["amount"];
		$orderid = $_POST["orderid"];
		$hash = $_POST["hash"];

		$transactionID = ($status == "OK" ? $_POST["transactionID"] : $_POST["transectionID"]);
		$hash_for_verify = ($status == "OK" ? hash_hmac('sha256', $transactionID.$amount, $this->getSecretKey()) : hash_hmac('sha256', $transactionID.$amount.$status.$orderid, $this->getSecretKey()));


		if ($hash == $hash_for_verify){ 

			return ["status_verify"=>"pass","data"=>["status" => $status, "transactionID" => $transactionID, "amount" => $amount, "orderid" => $orderid]];
		 
		}else{

			return ["status_verify"=>"fail"];
			
		}



	}
	 


}



?>