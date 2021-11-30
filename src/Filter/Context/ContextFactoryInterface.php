<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Context;

interface ContextFactoryInterface
{

	/**
	 * @param mixed[] $context
	 */
	public function create(array $context): ContextInterface;

}
