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
	//
	//public $key = "keyValue";
	//public $address = "credomatic.com";

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
			return $this->createResponse("error", "There is no controller for this request.", $request);
			break;
		}
	}

	function postResponse(Request $request){
		$this->extractData($dataRequest, json_decode($request->getContent()));
		$validation = $this->validPayment($dataRequest);
		if($validation){
			$this->setPaymentConfiguration($dataRequest);
			return $this->credomaticRequest($dataRequest, $request);
			//return $this->createResponse("ok", $dataRequest, $request);
		}
		return $this->createResponse("error", $validation, $request);
	}

	function credomaticRequest($dataRequest, $request){
		$client = \Drupal::httpClient();
		
		try {
			$response = $client->post(
				"https://credomatic.compassmerchantsolutions.com/api/transact.php",
				array(
					'headers' => array(
						/*'Accept' => 'application/json',
						'Content-type' => 'application/json'*/
						'Content-type' => 'application/x-www-form-urlencoded',
						'Accept' => 'text/plain'
						),
					'body' => json_encode($dataRequest)
					)
				);
			$dataResponse = (string) $response->getBody();
			return $this->createResponse("ok", $dataResponse, $request);
		}
		catch (RequestException $exception) {
			return $this->createResponse("error", $exception, $request);
		}
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

		/*if (is_null($object["cvv"])) {
			return 'Missing attribute: CVV';
		}

		if (is_null($object["orderid"])) {
			return 'Missing attribute: Order Id';
		}*/
		return true;
	}

	function extractData(&$object, $data){
		$object["type"] = $data->type;
		$object["ccnumber"] = $data->ccnumber;
		$object["ccexp"] = $data->ccexp;
		$object["amount"] = $data->amount;
		//$object["cvv"] = $data->cvv;
		//$object["orderid"] = $data->orderid;
	}

	function getHash($object){
		$str = "" . $object["orderid"] . "|" . $object["amount"];
		$str += "" . $object["time"] . "|" . $object["Key"];
		return md5($str);
	}

	function setPaymentConfiguration(&$object){
		//***Variables de configuración***
		$object["key_id"] = 449510;
    	//$object["processor_id"] = "123123";
		$object["redirect"] = "https://quickphoto.ddns.net/quickphoto";

		//***Variables generadas dinámicamente***
		$object["time"] = time();
		$object["hash"] = $this->getHash($object);
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

	function createResponse($result, $data, $request){
		$response["result"] = $result;
		$response["data"] = $data;
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response(json_encode($response), 200, $headers);
	}
};
