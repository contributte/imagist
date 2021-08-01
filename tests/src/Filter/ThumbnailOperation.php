<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

final class ThumbnailOperation implements OperationInterface
{

	public function supports(ImageFilter $filter, Scope $scope): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	public function operate(Image $image, ImageFilter $filter): void
	{
		$image->resize(15, 15, $image::EXACT);
	}

}
