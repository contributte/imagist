<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

interface ImagePreviewFactoryInterface
{

	public function create(): ImagePreviewInterface;

}
