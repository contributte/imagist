<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

interface PersisterRegistryInterface
{

	public function add(PersisterInterface $persister): void;

	public function persist(ImageInterface $image, ContextInterface $context): ImageInterface;

}
