<?php
namespace Dynamo\Middleware;

use http\Env\Request,
	http\Env\Response;

class CORS {
	private $domains, $methods, $allowHeaders, $exposeHeaders, $ttl, $credentials;
	public function __construct($domains = [], $credentials = false, $methods = [], $allowHeaders = [], $exposeHeaders = [], $ttl = 3500) {
		if(empty($domains)) {
			throw new \ArgumentException('You must supply at least one domain!');
		}
		$this->domains = is_array($domains) ? $domains : [$domains];
		$this->credentials = $credentials;
		$this->methods = $methods;
		$this->allowHeaders = $allowHeaders;
		$this->exposeHeaders = $exposeHeaders;
		$this->ttl = $ttl;
	}
	public function __invoke(Request $request, Response $response) {
		$origin = $request->getHeader('Origin');
		if(!empty($origin)) {
			if(in_array($origin, $this->domains)) {
				$response->setHeader('Access-Control-Allow-Origin', $origin);
				if($this->credentials) {
					$response->setHeader('Access-Control-Allow-Credentials', $this->credentials);
				}
				if(!empty($this->methods)) {
					$response->setHeader('Access-Control-Allow-Methods', implode(', ', $this->methods));
				}
				if(!empty($this->allowHeaders)) {
					$response->setHeader('Access-Control-Allow-Headers', implode(', ', $this->allowHeaders));
				}
				if(!empty($this->exposeHeaders)) {
					$response->setHeader('Access-Control-Expose-Headers', implode(', ', $this->exposeHeaders));
				}
				if(!empty($this->ttl)) {
					$response->setHeader('Access-Control-Max-Age', $this->ttl);
				}
			}
		}
	}
}