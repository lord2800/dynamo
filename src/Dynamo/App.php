<?php
namespace Dynamo;

use DI\Injector;

abstract class App {
	private $queue;
	/**
	  * @var \DI\Injector The injector for this application
	  * @see http://lord2800.github.io/di.php/coverage The DI\Injector Code Coverage results
	  */
	protected $injector;
	/**
	  * @var int The maximum number of loops through the generators after the first pass
	  */
	protected $maxIterations = 10;

	/**
	  * Generally you don't want to use this, and instead want to use the
	  * static create method.
	  * @param \DI\Injector The injector for the application
	  */
	public function __construct(Injector $injector) {
		$this->injector = $injector;
	}

	public static function create() {
		$injector = new Injector();
		$injector->provide('injector', $injector);

		return new static($injector);
	}

	public abstract function config();

	public function register(callable $middleware) {
		if(empty($this->queue)) {
			$this->queue = new \SplQueue();
		}
		$this->queue->enqueue($middleware);
	}

	public function run() {
		$this->config();

		$generators = [];
		foreach($this->queue as $middleware) {
			$boundFn = $this->injector->inject($middleware);
			$result = $boundFn();
			if($result instanceof \Generator) {
				array_unshift($generators, $result);
			}
		}
		$count = 0;

		while(!empty($generators)) {
			$len = count($generators);
			$clone = $generators;
			for($i = 0; $i < $len; $i++) {
				$middleware = $clone[$i];
				if(!$middleware->valid()) {
					array_splice($generators, $i, 1);
					continue;
				}
				$middleware->next();
			}

			$count++;
			if($count === $this->maxIterations) {
				$gl = '';
				foreach($generators as $g) {
					$gl .= new \ReflectionClass($g);
				}
				throw new \RuntimeException($this->maxIterations . ' iterations reached, aborting! Pending generators: ' . $gl);
			}
		}
	}
}
