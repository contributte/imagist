<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\AbstractFilterProcessor;
use Nette\Utils\Image;

final class NetteFilterProcessor extends AbstractFilterProcessor
{

	protected function createImageInstance(FileInterface $file): Image
	{
		return Image::fromString($file->getContent());
	}

	protected function imageInstanceToString(object $image, FileInterface $source, ContextImageAware $context): string
	{
		assert($image instanceof Image);

		return $image->toString($source->getMimeType()->getImageType(), $context->get('quality'));
	}

}
