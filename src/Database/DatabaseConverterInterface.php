<?php declare(strict_types = 1);

namespace Contributte\Imagist\Database;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;

interface DatabaseConverterInterface
{

	public function convertToDatabase(?ImageInterface $image): ?string;

	public function convertToPhp(?string $value, ?bool $nullable = null): ?PersistentImageInterface;

}
