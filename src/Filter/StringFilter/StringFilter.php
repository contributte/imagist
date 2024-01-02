<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use LogicException;

final class StringFilter implements FilterInterface
{

	private string $name;

	/** @var mixed[] */
	private array $arguments;

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(string $name, array $arguments = [])
	{
		$this->name = $name;
		$this->arguments = $arguments;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier($this->name, $this->arguments);
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOperations(): array
	{
		throw new LogicException(
			sprintf(
				'String filter is only case without operations. Please use %s to extract correct filter.',
				StringFilterCollectionInterface::class,
			)
		);
	}

}
