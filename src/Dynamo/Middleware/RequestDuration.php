<?php
namespace Dynamo\Middleware;

use Dynamo\HttpResponse;

class RequestDuration {
	public function __invoke(HttpResponse $response) {
		$start = microtime(true);
		yield;
		$duration = microtime(true) - $start;
		$response->setHeader('X-Request-Duration-MS', $duration * 1000);
	}
}
