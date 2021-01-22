<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;

final class EmptyImageRemover implements RemoverInterface
{

	public function supports(PersistentImageInterface $image): bool
	{
		return $image instanceof EmptyImageInterface;
	}

	public function remove(PersistentImageInterface $image): void
	{
		//throw new InvalidArgumentException('Cannot remove empty image');
	}

}
