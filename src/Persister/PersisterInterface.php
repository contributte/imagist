<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;

interface PersisterInterface
{

	public function supports(ImageInterface $image, Context $context): bool;

	public function persist(ImageInterface $image, Context $context): ImageInterface;

}
