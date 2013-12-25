<?php
namespace Dynamo;

class HttpResponse extends Proxy {
	public function __construct() { parent::__construct('http\\Env\\Response'); }
}
