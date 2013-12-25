<?php

use Dynamo\Middleware\CORS;

class CORSTest extends PHPUnit_Framework_TestCase {
	public function testCorsRespondsToOrigin() {
		$cors = new CORS('http://example.com');

		$request = $this->getMock('Dynamo\\HttpRequest', ['getHeader']);
		$request->expects($this->once())
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.com'));

		$response = $this->getMockBuilder('Dynamo\\HttpResponse')
						->disableOriginalConstructor()
						->setMethods(['setHeader'])
						->getMock();
		$response->expects($this->at(0))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Allow-Origin'), $this->equalTo('http://example.com'));
		$response->expects($this->at(1))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Max-Age'), $this->equalTo('3500'));

		$cors($request, $response);
	}

	public function testCorsIgnoresUnknownDomains() {
		$cors = new CORS('http://example.com');

		$request = $this->getMock('Dynamo\\HttpRequest', ['getHeader']);
		$request->expects($this->any())
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.org'));

		$this->assertEquals('http://example.org', $request->getHeader('Origin'));

		$response = $this->getMockBuilder('Dynamo\\HttpResponse')->disableOriginalConstructor()->getMock();

		$cors($request, $response);

		$this->assertNull($response->getHeader('Access-Control-Allow-Origin'));
	}
}