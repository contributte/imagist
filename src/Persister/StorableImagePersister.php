<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\Resolver\FileNameResolverInterface;

final class StorableImagePersister extends ImagePersisterAbstract
{

	private FileNameResolverInterface $fileNameResolver;

	public function __construct(
		FileFactoryInterface $fileFactory,
		FilterProcessorInterface $filterProcessor,
		FileNameResolverInterface $fileNameResolver
	)
	{
		parent::__construct($fileFactory, $filterProcessor);
		$this->fileNameResolver = $fileNameResolver;
	}

	public function supports(ImageInterface $image, ContextInterface $context): bool
	{
		return $image instanceof StorableImageInterface;
	}

	public function persist(ImageInterface $image, ContextInterface $context): ImageInterface
	{
		assert($image instanceof StorableImageInterface);

		$result = $image->withName($this->fileNameResolver->resolve($this->fileFactory->create($image)));

		$this->save($result, $context);
		$image->close('image persisted');

		return $result;
	}

}
