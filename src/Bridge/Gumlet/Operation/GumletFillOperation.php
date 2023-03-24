<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\Operation\OperationAsFilter;

final class GumletFillOperation extends OperationAsFilter
{

	private string $fill;

	public function __construct(string $fill)
	{
		$this->fill = $fill;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('gumlet_fill', [$this->fill]);
	}

	public function getFill(): string
	{
		return $this->fill;
	}

}
