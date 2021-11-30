<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\OperationInterface;

/**
 * @internal
 */
final class DecorateDynamicFilter implements FilterInterface
{

	private string $name;

	private FilterInterface $filter;

	/** @var mixed[] */
	private array $arguments;

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(string $name, FilterInterface $filter, array $arguments)
	{
		$this->name = $name;
		$this->filter = $filter;
		$this->arguments = $arguments;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier($this->name, $this->arguments);
	}

	/**
	 * @return OperationInterface[]
	 */
	public function getOperations(): array
	{
		return $this->filter->getOperations();
	}

}
