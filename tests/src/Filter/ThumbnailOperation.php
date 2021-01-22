<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

final class ThumbnailOperation implements OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	public function operate(Image $image, FilterInterface $filter): void
	{
		$image->resize(15, 15, $image::EXACT);
	}

}
