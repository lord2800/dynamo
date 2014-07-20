<?php

use Dynamo\Middleware\RequestDuration;

class RequestDurationTest extends PHPUnit_Framework_TestCase {
	public function testShouldAddXRequestDurationMSHeader() {
		$duration = new RequestDuration();
		$response = $this->getMockBuilder('Dynamo\\HttpResponse')
						->disableOriginalConstructor()
						->setMethods(['setHeader'])
						->getMock();
		$response->expects($this->once())
				->method('setHeader')
				->with($this->equalTo('X-Request-Duration'), $this->greaterThan(0));

		$duration($response)->next();
	}
}