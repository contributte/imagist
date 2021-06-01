<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config\Operation;

use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Filter\FilterInterface;
use InvalidArgumentException;
use Nette\Utils\Image;

final class CropConfigOperation implements NetteConfigOperationInterface
{

	public function getName(): string
	{
		return 'crop';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
		if (count($arguments) !== 4) {
			throw new InvalidArgumentException('Operation crop must have 4 arguments exactly.');
		}

		$image->crop(...$arguments);
	}

}
