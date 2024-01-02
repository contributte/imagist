<?php declare(strict_types = 1);

namespace Tests\Testing\Filter;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\ResizeOperation;

final class ThumbnailFilter implements FilterInterface
{

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('thumbnail');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOperations(): array
	{
		return [
			new ResizeOperation(15, 15, 'exact'),
		];
	}

}
