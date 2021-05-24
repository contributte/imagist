<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config\Operation;

use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Filter\FilterInterface;
use Nette\Utils\Image;

final class SharpenConfigOperation implements NetteConfigOperationInterface
{

	public function getName(): string
	{
		return 'sharpen';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function apply(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
		$image->sharpen();
	}

}
