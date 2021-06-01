<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config\Operation;

use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Filter\FilterInterface;
use InvalidArgumentException;
use LogicException;
use Nette\Utils\Image;

final class ResizeConfigOperation implements NetteConfigOperationInterface
{

	public function getName(): string
	{
		return 'resize';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
		[$width, $height, $flag] = array_replace([
			1 => null,
			2 => 'fit',
		], $arguments);

		if (count($arguments) !== 3) {
			throw new InvalidArgumentException('Config filter resize must have at least one argument.');
		}

		$image->resize($width, $height, $this->flagToInt($flag));
	}

	private function flagToInt(string $flag): int
	{
		switch ($flag) {
			case 'fit':
				return Image::FIT;
			case 'fill':
				return Image::FILL;
			case 'exact':
				return Image::EXACT;
			case 'shrink_only':
				return Image::SHRINK_ONLY;
			case 'stretch':
				return Image::STRETCH;
			default:
				throw new LogicException(sprintf('Resize flag %s not exists.', $flag));
		}
	}

}
