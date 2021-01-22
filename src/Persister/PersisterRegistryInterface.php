<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;

interface PersisterRegistryInterface
{

	public function add(PersisterInterface $persister): void;

	public function persist(ImageInterface $image): ImageInterface;

}
