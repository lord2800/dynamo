<?php
namespace Dynamo;

/**
  * This class isn't worth testing because it's a thin wrapper around http\Env\Request
  */
/** @codeCoverageIgnore */
class HttpRequest extends Proxy {
	public function __construct() { parent::__construct('http\\Env\\Request'); }
}
