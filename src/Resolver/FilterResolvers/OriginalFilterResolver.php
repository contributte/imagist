<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FilterResolvers;

use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Resolver\FilterResolverInterface;

final class OriginalFilterResolver implements FilterResolverInterface
{

	public function resolve(ImageFilter $filter): string
	{
		return '_' . $filter->getName();
	}

}
