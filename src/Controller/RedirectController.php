<?php
/**
@file
Contains \Drupal\api_ecommerce\Controller\RedirectController.
 */

namespace Drupal\api_ecommerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Component\Serialization\Json;
use Drupal\file\Entity\File;


class RedirectController extends ControllerBase {

	public function response(RouteMatchInterface $route_match, Request $request) {
		return $this->handle($request);
	}

	function handle(Request $request) {
		$this->createLog(
			"/var/www/html/drupal-8.1.8/logCredomaticRequest.txt", 
			(string)$request->getContent());
		/*switch ($request->getMethod())
		{
			case 'POST':
			return $this->createResponse("ok", json_decode($request->getContent()), $request);
			break;

			case 'GET':
			return $this->createResponse("ok", json_decode($request->getContent()), $request);
			break;

			default:
			return $this->createResponse("error", "There is no controller for this request.", $request);
			break;
		}*/
	}

	function createResponse($result, $data, $request){
		$response["result"] = $result;
		$response["data"] = $data;
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response(json_encode($response), 200, $headers);
	}

	function createLog($filename, $value){
		$myfile = fopen($filename, "w");
		fwrite($myfile, $value);
		fclose($myfile);
	}
};
