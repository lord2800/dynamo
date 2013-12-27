<?php
namespace Dynamo;

/**
  * This class is untestable because http\Env\Response can't be tested
  */
/** @codeCoverageIgnore */
class HttpResponse extends Proxy {
	public function __construct() { parent::__construct('http\\Env\\Response'); }
	public function setBody($body) {
		if(is_string($body)) {
			$b = new http\Message\Body();
			$b->append($body);
			$body = $b;
		} else if(is_resource($body)) {
			$body = new http\Message\Body($body);
		}
		parent::setBody($body);
	}
}
