<?php
namespace Dynamo;

use \ReflectionClass,
	\ReflectionMethod;

class Proxy {
	private $inst;
	private $methods = [];
	private $statics = [];

	public function __construct($className) {
		$class = new ReflectionClass($className);
		$args = func_get_args();
		array_pop($args);
		$this->inst = $class->newInstanceArgs($args);

		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC && ~ReflectionMethod::IS_ABSTRACT && ~ReflectionMethod::IS_STATIC);
		foreach($methods as $method) {
			$this->methods[$method->getName()] = $method;
		}

		$statics = $class->getMethods(ReflectionMethod::IS_PUBLIC && ~ReflectionMethod::IS_ABSTRACT && ~ReflectionMethod::IS_STATIC);
		foreach($statics as $static) {
			$this->statics[$static->getName()] = $static;
		}
	}
	public function __get($name) { return $this->inst->$name; }
	public function __set($name, $value) { return $this->inst->$name = $value; }
	public function __isset($name) { return isset($this->inst->$name); }
	public function __unset($name) { unset($this->inst->$name); }
	public function __call($name, $arguments) { return $this->methods[$name]->invokeArgs($arguments); }
	public static function __callStatic($name, $arguments) { return $this->statics[$name]->invokeArgs($arguments); }
}
