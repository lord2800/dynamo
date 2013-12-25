<?php

use Dynamo\Middleware\CORS;

class CORSTest extends PHPUnit_Framework_TestCase {
	public function testCorsRespondsToOrigin() {
		$cors = new CORS('http://example.com');

		$request = $this->getMock('http\\Env\\Request');
		$request->expects($this->once())
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.com'));

		$response = $this->getMock('http\\Env\\Response');
		$response->expects($this->once())
				->method('setHeader')
				->with($this->equalTo('Access-Control-Allow-Origin'), $this->equalTo('http://example.com'));

		$cors($request, $response);
	}

	public function testCorsIgnoresUnknownDomains() {
		$cors = new CORS('http://example.com');

		$request = $this->getMock('http\\Env\\Request');
		$request->expects($this->once())
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.org'));

		$response = $this->getMock('http\\Env\\Response');
		$cors($request, $response);
	}
}