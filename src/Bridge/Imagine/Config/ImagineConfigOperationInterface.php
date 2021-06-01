<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine\Config;

use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

interface ImagineConfigOperationInterface
{

	public function getName(): string;

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(ImageInterface $image, FilterInterface $filter, array $arguments): void;

}
