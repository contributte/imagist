<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

interface OperationInterface
{

	public function supports(ImageFilter $filter, Scope $scope): bool;

	public function operate(Image $image, ImageFilter $filter): void;

}
