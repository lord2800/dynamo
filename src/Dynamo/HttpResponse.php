<?php
namespace Dynamo;

/**
  * This class is untestable because http\Env\Response can't be tested
  */
/** @codeCoverageIgnore */
class HttpResponse extends Proxy {
	public function __construct() { parent::__construct('http\\Env\\Response'); }
}
