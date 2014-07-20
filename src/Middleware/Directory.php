<?php
namespace Dynamo\Middleware;

use Dynamo\HttpRequest;
use Dynamo\HttpResponse;
use \finfo;

class Directory {
	private $path, $filter, $index, $hasIndex, $finfo;
	public function __construct($path, callable $filter = null, $index = false) {
		$this->path = $path;
		$this->path .= substr($this->path, -1) === '/' ? '' : DIRECTORY_SEPARATOR;

		if(!is_dir($this->path)) {
			throw new \InvalidArgumentException(sprintf('%s does not exist!', $path));
		}
		$this->filter = !empty($filter) ? $filter : function ($path) { return file_exists($path); };
		$this->finfo = new finfo();
		$this->hasIndex = is_callable($index);
		$this->index = $index;
	}

	public function __invoke(HttpRequest $request, HttpResponse $response) {
		$path = $this->path . substr($request->getUrl(), 1);
		$fn = $this->filter;
		if($fn($path)) {
			$response->setContentEncoding(HttpResponse::GZIP);

			if(is_dir($path) && $this->hasIndex) {
				// serve the index of the specified path
				$fn = $this->index;
				$response->setContentType('text/html');
				$response->setBody($fn($path));
			} else if(is_file($path)) {
				$response->setContentType($this->finfo->file($path, FILEINFO_MIME_TYPE));
				$response->setBody(fopen($path, 'r'));
			}
		}
	}
}
