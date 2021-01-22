<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FilterResolvers;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Resolver\FilterResolverInterface;

final class OriginalFilterResolver implements FilterResolverInterface
{

	public function resolve(FilterInterface $filter): string
	{
		return '_' . $filter->getName();
	}

}
