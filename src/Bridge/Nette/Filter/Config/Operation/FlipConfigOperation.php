<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config\Operation;

use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Filter\FilterInterface;
use InvalidArgumentException;
use Nette\Utils\Image;

final class FlipConfigOperation implements NetteConfigOperationInterface
{

	public function getName(): string
	{
		return 'flip';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function apply(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
		if (!count($arguments)) {
			throw new InvalidArgumentException('Operation flip must have one argument.');
		}

		$image->flip($this->flipToInt(...$arguments));
	}

	private function flipToInt(string $mode): int
	{
		switch ($mode) {
			case 'horizontal':
				return IMG_FLIP_HORIZONTAL;
			case 'vertical':
				return IMG_FLIP_VERTICAL;
			case 'both':
				return IMG_FLIP_BOTH;
			default:
				throw new InvalidArgumentException('Operation flip value must be one of horizontal, vertical or both');
		}
	}

}
