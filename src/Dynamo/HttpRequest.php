<?php
namespace Dynamo;

class HttpRequest extends Proxy {
	public function __construct() { parent::__construct('http\\Env\\Request'); }
}
