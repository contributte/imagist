<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Bridge\Nette\Filter\Exceptions\OperationNotFoundException;
use Contributte\Imagist\Context\Context;
use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Nette\Utils\Image;

final class NetteFilterProcessor implements FilterProcessorInterface
{

	private NetteOperationRegistryInterface $operationRegistry;

	public function __construct(NetteOperationRegistryInterface $operationRegistry)
	{
		$this->operationRegistry = $operationRegistry;
	}

	public function process(FileInterface $target, FileInterface $source, Context $context): string
	{
		$filter = $target->getImage()->getFilter();
		if (!$filter) {
			return $target->getContent();
		}

		$operation = $this->operationRegistry->get($filter, $target->getImage()->getScope());

		if (!$operation) {
			throw new OperationNotFoundException($target->getImage());
		}

		$operation->operate($image = $this->createImageInstance($source), $filter, $options = new NetteImageOptions());

		return $image->toString($source->getMimeType()->getImageType(), $options->getQuality());
	}

	private function createImageInstance(FileInterface $file): Image
	{
		return Image::fromString($file->getContent());
	}

}
