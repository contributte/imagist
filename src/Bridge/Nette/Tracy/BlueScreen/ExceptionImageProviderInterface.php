<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen;

use Contributte\Imagist\Entity\ImageInterface;

interface ExceptionImageProviderInterface
{

	public function getImage(): ImageInterface;

}
