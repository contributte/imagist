<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;

abstract class ImagePersisterAbstract implements PersisterInterface
{

	protected FileFactoryInterface $fileFactory;

	protected FilterProcessorInterface $filterProcessor;

	public function __construct(FileFactoryInterface $fileFactory, FilterProcessorInterface $filterProcessor)
	{
		$this->fileFactory = $fileFactory;
		$this->filterProcessor = $filterProcessor;
	}

	protected function save(ImageInterface $image, Context $context): void
	{
		$target = $this->fileFactory->create($image);
		$source = $this->fileFactory->create($image->getOriginal());

		$target->setContent(
			$this->filterProcessor->process($target, $source, $context)
		);
	}

}
