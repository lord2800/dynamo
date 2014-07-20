<?php
namespace Dynamo;

/**
  * This class is covered by Sabre's own tests
  * TODO finish covering the methods added to it
  * TODO move to using a ResponseDecorator instead
  */
/** @codeCoverageIgnore */
class HttpResponse extends \Sabre\HTTP\Response {
	const NONE = 0;
	const GZIP = 1;

	public function __construct() {
		parent::__construct();
		static::setStatus(200);
	}

	public function setContentEncoding($encoding = self::NONE) {
		if(!is_numeric($encoding) || $encoding < 0 || $encoding > 1) {
			throw new \InvalidArgumentException('Invalid encoding type.');
		}
		if($encoding === self::GZIP) {
			// TODO figure out how to gzip the stream directly as an output buffer
		} else if($encoding === self::NONE) {
			// TODO disable encoding
		}
	}

	public function setContentType($type = 'text/plain') {
		static::setHeader('Content-Type', $type);
	}

    public function send() {
        \Sabre\HTTP\Sapi::sendResponse($this);
    }
}
