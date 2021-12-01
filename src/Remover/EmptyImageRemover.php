<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

final class EmptyImageRemover implements RemoverInterface
{

	public function supports(PersistentImageInterface $image, ContextInterface $context): bool
	{
		return $image instanceof EmptyImageInterface;
	}

	public function remove(PersistentImageInterface $image, ContextInterface $context): void
	{
		//throw new InvalidArgumentException('Cannot remove empty image');
	}

}
