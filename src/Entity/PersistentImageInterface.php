<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

interface PersistentImageInterface extends ImageInterface
{

	public function close(?string $reason = null): void;

}
