<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface RemoverInterface
{

	public function supports(PersistentImageInterface $image): bool;

	public function remove(PersistentImageInterface $image): void;

}
