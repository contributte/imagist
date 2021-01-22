<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\LinkGeneratorInterface;

final class ImagePreviewFactory implements ImagePreviewFactoryInterface
{

	private LinkGeneratorInterface $linkGenerator;

	public function __construct(LinkGeneratorInterface $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	public function create(): ImagePreviewInterface
	{
		return new ImagePreview($this->linkGenerator);
	}

}
