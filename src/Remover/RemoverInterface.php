<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

interface RemoverInterface
{

	public function supports(PersistentImageInterface $image, ContextInterface $context): bool;

	public function remove(PersistentImageInterface $image, ContextInterface $context): void;

}
