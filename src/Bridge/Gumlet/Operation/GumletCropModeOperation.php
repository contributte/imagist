<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\Operation\OperationAsFilter;

final class GumletCropModeOperation extends OperationAsFilter
{

	private string $mode;

	public function __construct(string $mode)
	{
		$this->mode = $mode;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('gumlet_crop', [$this->mode]);
	}

	public function getMode(): string
	{
		return $this->mode;
	}

}
