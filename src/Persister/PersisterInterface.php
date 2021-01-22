<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;

interface PersisterInterface
{

	public function supports(ImageInterface $image): bool;

	public function persist(ImageInterface $image): ImageInterface;

}
