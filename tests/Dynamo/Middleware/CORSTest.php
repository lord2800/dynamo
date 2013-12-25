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

	public function testCorsRespondsToMultipleOriginDomains() {
		$cors = new CORS(['http://example.com', 'http://example.org']);

		$request = $this->getMock('Dynamo\\HttpRequest', ['getHeader']);

		$request->expects($this->at(0))
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.com'));
		$request->expects($this->at(1))
				->method('getHeader')
				->with($this->equalTo('Origin'))
				->will($this->returnValue('http://example.org'));

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

		$response->expects($this->at(2))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Allow-Origin'), $this->equalTo('http://example.org'));
		$response->expects($this->at(3))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Max-Age'), $this->equalTo('3500'));

		$cors($request, $response);
		$cors($request, $response);
	}

	public function testCorsHandlesAllArgumentsCorrectly() {
		$cors = new CORS('http://example.com', true, 'X-Method', 'X-Response', 'X-Exposed', 500);

		$request = $this->getMock('Dynamo\\HttpRequest', ['getHeader']);

		$request->expects($this->at(0))
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
				->with($this->equalTo('Access-Control-Allow-Credentials'), $this->equalTo('true'));
		$response->expects($this->at(2))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Allow-Methods'), $this->equalTo('X-Method'));
		$response->expects($this->at(3))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Allow-Headers'), $this->equalTo('X-Response'));
		$response->expects($this->at(4))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Expose-Headers'), $this->equalTo('X-Exposed'));
		$response->expects($this->at(5))
				->method('setHeader')
				->with($this->equalTo('Access-Control-Max-Age'), $this->equalTo('500'));

		$cors($request, $response);
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

	/**
	  * @expectedException InvalidArgumentException
	  * @expectedExceptionMessage You must supply at least one domain
	  */
	public function testEmptyDomainListShouldThrow() {
		$cors = new CORS();
		$cors($this->getMock('Dynamo\\HttpRequest', ['getHeader']), $this->getMock('Dynamo\\HttpResponse', ['setHeader']));
	}
}