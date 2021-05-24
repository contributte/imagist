<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine\Config\Operation;

use Contributte\Imagist\Bridge\Imagine\Config\ImagineConfigOperationInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use InvalidArgumentException;

final class ResizeConfigOperation implements ImagineConfigOperationInterface
{

	public function getName(): string
	{
		return 'resize';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(ImageInterface $image, FilterInterface $filter, array $arguments): void
	{
		if (count($arguments) !== 2) {
			throw new InvalidArgumentException('Config filter resize must have two arguments.');
		}

		$image->resize(new Box(...$arguments));
	}

}
