<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Resource\ResourceFactoryInterface;
use Nette\Utils\Image;
use Typertion\Php\TypeAssert;

final class NetteResourceFactory implements ResourceFactoryInterface
{

	public const QUALITY_CONTEXT = 'quality';

	public function create(FileInterface $source, ContextInterface $context): object
	{
		return Image::fromString($source->getContent());
	}

	public function toString(object $resource, FileInterface $source, ContextInterface $context): string
	{
		assert($resource instanceof Image);

		return $resource->toString(
			$source->getMimeType()->getImageType(),
			TypeAssert::intOrNull($context->get(self::QUALITY_CONTEXT))
		);
	}

}
