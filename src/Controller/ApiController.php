<?php
/**
@file
Contains \Drupal\api_ecommerce\Controller\ApiController.
 */

namespace Drupal\api_ecommerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\RouteMatchInterface;


class ApiController extends ControllerBase {
	//$key = "";

	public function content(RouteMatchInterface $route_match, Request $request) {
		//usar $key en request a Credomatic
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response(json_encode("$api"), 200, $headers);
	}

	public function admin() {

		return array(
			'#type' => 'markup',
			'#markup' => t('Administrador de ecommerce'),
			);
	}
};
