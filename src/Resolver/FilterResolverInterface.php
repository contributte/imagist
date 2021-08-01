<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver;

use Contributte\Imagist\Entity\Filter\ImageFilter;

interface FilterResolverInterface
{

	public function resolve(ImageFilter $filter): string;

}
