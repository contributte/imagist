<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;

interface PersisterRegistryInterface
{

	public function add(PersisterInterface $persister): void;

	public function persist(ImageInterface $image, Context $context): ImageInterface;

}
