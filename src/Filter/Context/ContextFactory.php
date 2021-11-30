<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Context;

final class ContextFactory implements ContextFactoryInterface
{

	public function create(array $context): ContextInterface
	{
		return new Context($context);
	}

}
