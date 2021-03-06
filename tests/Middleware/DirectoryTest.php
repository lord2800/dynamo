<?php

use Dynamo\Middleware\Directory;
use org\bovigo\vfs\vfsStream;

class DirectoryTest extends PHPUnit_Framework_TestCase {
	private $dir;
	public function setUp() {
		$this->dir = vfsStream::setup('docroot', null, ['body.txt' => 'hello, world']);
	}

	public function testDirectoryShouldServeFilesInsideIt() {
		$directory = new Directory(vfsStream::url('docroot'));

		$request = $this->getMockBuilder('Dynamo\\HttpRequest')
						->disableOriginalConstructor()
						->setMethods(['getUrl'])
						->getMock();
		$request->expects($this->once())
				->method('getUrl')
				->will($this->returnValue('/body.txt'));

		$response = $this->getMockBuilder('Dynamo\\HttpResponse')
						 ->disableOriginalConstructor()
						 ->setMethods(['setBody', 'setContentType', 'setContentEncoding'])
						 ->getMock();
		$response->expects($this->once())
				 ->method('setContentType')
				 ->with($this->stringContains('text/plain'));
		$response->expects($this->once())
				 ->method('setContentEncoding')
				 ->with($this->equalTo(1));
		$response->expects($this->once())
				 ->method('setBody')
				 ->with($this->callback(function ($in) {
					return (is_resource($in)) && (rewind($in) && stream_get_contents($in) === 'hello, world');
				 }));

		$directory($request, $response);
	}

	public function testDirectoryShouldServeTheIndexIfNotAFile() {
		$directory = new Directory(vfsStream::url('docroot'), null, function () { return 'hello, world'; });

		$request = $this->getMockBuilder('Dynamo\\HttpRequest')
						->disableOriginalConstructor()
						->setMethods(['getUrl'])
						->getMock();
		$request->expects($this->once())
				->method('getUrl')
				->will($this->returnValue('/'));

		$response = $this->getMockBuilder('Dynamo\\HttpResponse')
						 ->disableOriginalConstructor()
						 ->setMethods(['setBody', 'setContentType', 'setContentEncoding'])
						 ->getMock();
		$response->expects($this->once())
				 ->method('setContentType')
				 ->with($this->stringContains('text/html'));
		$response->expects($this->once())
				 ->method('setContentEncoding')
				 ->with($this->equalTo(1));
		$response->expects($this->once())
				 ->method('setBody')
				 ->with($this->callback(function ($in) {
					return (is_string($in)) && ($in === 'hello, world');
				 }));

		$directory($request, $response);
	}

	/**
	  * @expectedException InvalidArgumentException
	  * @expectedExceptionMessage does not exist
	 */
	public function testDirectoryShouldThrowIfNotFound() {
		$directory = new Directory('nevergonnaexistbecausekittens');
	}
}