<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface RemoverRegistryInterface
{

	public function add(RemoverInterface $remover): void;

	public function remove(PersistentImageInterface $image): void;

}
