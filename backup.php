<?php
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\RouteMatchInterface;


class ApiController implements ContainerAwareInterface {
	use ContainerAwareTrait;

	public function handle(RouteMatchInterface $route_match, Request $request)
	{
		/*switch ($api)
		{
			case ‘custom’:
			return static::handle($request);
			break;
		}*/
		$output = json_encode('OK');
		file_save_data($output, '/var/www/html/drupal-8.1.8/log/log.txt');
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response($output, 200, $headers);
		//return new Response(null, 404, array());
	}

	/*public static function handle(Request $request) {
		switch ($request->getMethod())
		{
			case ‘GET’:
			return static::get($request);
			break;

			default:
			return new Response(null, 404, array());
			break;
		}

		return static::get($request);
	}

	private static function get(Request $request) 
	{
		$output = json_encode('OK');
		$headers = array(‘Content-Type’ => $request->getMimeType(‘json’));
		return new Response($output, 200, $headers);
	}*/
}
