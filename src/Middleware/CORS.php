<?php
namespace Dynamo\Middleware;

use Dynamo\HttpRequest,
	Dynamo\HttpResponse;

class CORS {
	private $domains, $methods, $allowHeaders, $exposeHeaders, $ttl, $credentials;
	public function __construct($domains = [], $credentials = false, $methods = [], $allowHeaders = [], $exposeHeaders = [], $ttl = 3500) {
		if(empty($domains)) {
			throw new \InvalidArgumentException('You must supply at least one domain!');
		}
		$this->domains = is_array($domains) ? $domains : [$domains];
		$this->credentials = $credentials;
		$this->methods = is_array($methods) ? $methods : [$methods];
		$this->allowHeaders = is_array($allowHeaders) ? $allowHeaders : [$allowHeaders];
		$this->exposeHeaders = is_array($exposeHeaders) ? $exposeHeaders : [$exposeHeaders];
		$this->ttl = (int)$ttl;
	}
	public function __invoke(HttpRequest $request, HttpResponse $response) {
		$origin = $request->getHeader('Origin');
		if(!empty($origin)) {
			if(in_array($origin, $this->domains)) {
				$response->setHeader('Access-Control-Allow-Origin', $origin);
				if(!!$this->credentials) {
					$response->setHeader('Access-Control-Allow-Credentials', !!$this->credentials ? 'true' : 'false');
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