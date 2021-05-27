<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config;

use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Filter\FilterInterface;
use Nette\Utils\Image;

interface NetteConfigOperationInterface
{

	public function getName(): string;

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void;

}
