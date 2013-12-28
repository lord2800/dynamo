<?php
namespace Dynamo;

/**
  * This class is covered by Sabre's own tests
  */
/** @codeCoverageIgnore */
class HttpRequest extends \Sabre\HTTP\RequestDecorator {
	public static function create() { return new static(\Sabre\HTTP\Request::createFromPHPRequest()); }
}
