<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;

final class MaskOperation extends OperationAsFilter
{

	private string $mask;

	public function __construct(string $mask)
	{
		$this->mask = $mask;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('crop', [$this->mask]);
	}

	public function getMask(): string
	{
		return $this->mask;
	}

}
