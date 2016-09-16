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
	public function content(RouteMatchInterface $route_match, Request $request, $api) {
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response(json_encode($api), 200, $headers);
	}
};
