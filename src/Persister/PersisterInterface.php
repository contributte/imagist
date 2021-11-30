<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

interface PersisterInterface
{

	public function supports(ImageInterface $image, ContextInterface $context): bool;

	public function persist(ImageInterface $image, ContextInterface $context): ImageInterface;

}
