<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver;

use Contributte\Imagist\Filter\FilterInterface;

interface FilterResolverInterface
{

	public function resolve(FilterInterface $filter): string;

}
