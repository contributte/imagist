<?php declare(strict_types = 1);

namespace Contributte\Imagist\Exceptions;

use Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen\BlueScreenBacktraceInterface;
use LogicException;

class ClosedImageException extends LogicException implements BlueScreenBacktraceInterface
{

	private $backTrace = [];

	public function __construct(string $message, array $backTrace = [])
	{
		$this->backTrace = $backTrace;
		
		parent::__construct($message);
	}

	public function getBackTrace(): array
	{
		return $this->backTrace;
	}

}
