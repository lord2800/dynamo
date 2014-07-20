<?php
namespace Dynamo;

use \ReflectionClass,
	\ReflectionMethod;

class Proxy {
	private $inst;
	private $methods = [];

	public function __construct($className) {
		$class = new ReflectionClass($className);
		$args = func_get_args();
		array_shift($args);
		$this->inst = $class->newInstanceArgs($args);

		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($methods as $method) {
			if($method->isAbstract() || $method->isStatic()) {
				continue;
			}
			$this->methods[$method->getName()] = $method;
		}
	}
	public function __get($name) { return $this->inst->$name; }
	public function __set($name, $value) { return $this->inst->$name = $value; }
	public function __isset($name) { return isset($this->inst->$name); }
	public function __unset($name) { unset($this->inst->$name); }
	public function __call($name, $arguments) { return $this->methods[$name]->invokeArgs($this->inst, $arguments); }
}
