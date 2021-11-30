<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Internal;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\OperationInterface;

/**
 * @internal
 */
final class VoidFilter implements FilterInterface
{

	private string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier($this->id);
	}

	/**
	 * @return OperationInterface[]
	 */
	public function getOperations(): array
	{
		return [];
	}

}
