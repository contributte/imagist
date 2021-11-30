<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FilterResolvers;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use LogicException;

final class OriginalFilterResolver implements FilterResolverInterface
{

	private bool $throwOnArguments;

	public function __construct(bool $throwOnArguments = true)
	{
		$this->throwOnArguments = $throwOnArguments;
	}

	public function resolve(FilterInterface $filter): string
	{
		if ($this->throwOnArguments && $filter->getIdentifier()->getArguments()) {
			throw new LogicException(
				sprintf(
					'This resolver is not recommended for dynamic filters, please use %s or %s resolver or disable this exception.',
					MD5FilterResolver::class,
					SimpleFilterResolver::class
				)
			);
		}

		return '_' . $filter->getIdentifier()->getName();
	}

}
