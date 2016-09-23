<?php
/**
@file
Contains \Drupal\api_ecommerce\Controller\SaleAuthCreditController.
 */

namespace Drupal\api_ecommerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Component\Serialization\Json;


class SaleAuthCreditController extends ControllerBase {

	public function response(RouteMatchInterface $route_match, Request $request) {
		return $this->handle($request);
	}

	function handle(Request $request) {
		switch ($request->getMethod())
		{
			case 'POST':
			return $this->postResponse($request);
			break;

			default:
			return $this->createJsonResponse("error", "There is no controller for this request.");
			break;
		}
	}

	function postResponse(Request $request){
		$this->extractData($dataRequest, json_decode($request->getContent()));
		$validation = $this->validPayment($dataRequest);
		if($validation){
			$this->setPaymentConfiguration($dataRequest);
			return $this->credomaticRequest($dataRequest);
			//return $this->createJsonResponse("ok", $dataRequest);
		}
		return $this->createJsonResponse("error", $validation);
	}

	function credomaticRequest($dataRequest){
		$url = "https://credomatic.compassmerchantsolutions.com/api/transact.php";
		foreach($dataRequest as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		//Configuración de request a Credomatic
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Content-Type: application/x-www-form-urlencoded',
				'Accept-Encoding: gzip, deflate, br'
				)
			);
		$response = curl_exec($ch);
		$reponseInfo = curl_getinfo($ch);
		curl_close($ch);

		//Sacar información del post
		//$html = new DOMDocument();
		//$html->loadHTML($response);

		$debugInformation = [
		"dataRequest" => $dataRequest,
		"reponseInfo" => $reponseInfo,
		"response" => $response,
		"html" => $html
		];

		return $this->createJsonResponse("ok", $debugInformation);
	}

	function extractData(&$object, $data){
		$object["type"] = $data->type;
		$object["ccnumber"] = $data->ccnumber;
		$object["ccexp"] = $data->ccexp;
		$object["amount"] =  (string) number_format($data->amount, 2);
		//$object["cvv"] = $data->cvv;
		//$object["orderid"] = $data->orderid;
	}

	function validPayment($object) {
		//************************************************
		if (is_null($object["type"])) {
			return 'Missing attribute: Transaction type';
		}
		if ($object["type"]!="sale" || $object["type"]!="auth" || $object["type"]!="credit") {
			return 'Invalid attribute: Transaction type';
		}

		//************************************************
		if (is_null($object["ccnumber"])) {
			return 'Missing attribute: Credit card number';
		}
		/*if (false == $this->get_card_type($object->ccnumber)) {
			return 'Invalid attribute: Credit card number';
		}*/

		//************************************************
		if (is_null($object["ccexp"])) {
			return 'Missing attribute: Credit card expiration date';
		}

		//************************************************
		if (is_null($object["amount"])) {
			return 'Missing attribute: Amount';
		}

		/*
		if (is_null($object["cvv"])) {
			return 'Missing attribute: CVV';
		}

		if (is_null($object["orderid"])) {
			return 'Missing attribute: Order Id';
		}
		*/
		return true;
	}

	function setPaymentConfiguration(&$object){
		//***Variables de configuración***
		$object["key_id"] = "6368074";
		$object["proccesor_id"] = "INET1125";
		$object["orderid"] = "CredomaticTest";
		$object["redirect"] = urlencode("http://quickphoto.ddns.net/quickphoto/redirect");

		//***Variables generadas dinámicamente***
		$object["time"] = time();
		$object["hash"] = $this->createHash($object);
	}

	function createHash($object){
		$key_hash = "3Yep7vKc7Y3vGP37TsZ83M3dFGPfcbCj";
		$str = "" . $object["orderid"] . "|" .  $object["amount"] . "|";
		$str = $str . $object["time"] . "|" . $key_hash;
		return md5($str);
	}

	function get_card_type($numberParam) {
		if(is_null($numberParam)){
			return false;
		}
		$number = preg_replace('/[^\d]/', '', $numberParam);

		if (preg_match('/^3[47][0-9]{13}$/', $number)) {
			return 'american-express';
		}
		elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $number)) {
			return 'diners';
		}
		elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number)) {
			return 'discover';
		}
		elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/', $number)) {
			return 'jcb';
		}
		elseif (preg_match('/^5[1-5][0-9]{14}$/', $number)) {
			return 'mastercard';
		}
		elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) {
			return 'visa';
		}
		else {
			return false;
		}
	}

	function createJsonResponse($result, $data){
		$response["result"] = $result;
		$response["data"] = $data;
		$headers = array("Content-Type" => "application/json");
		return new Response(json_encode($response), 200, $headers);
	}

	function createLog($filename, $value){
		$myfile = fopen($filename, "w");
		fwrite($myfile, $value);
		fclose($myfile);
	}
};
