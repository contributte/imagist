<?php declare(strict_types = 1);

namespace Contributte\Imagist\Builder;

use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationProcessor;
use Contributte\Imagist\Bridge\Nette\Filter\NetteResourceFactory;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterProcessor;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Persister\ChainImagePersister;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\ChainImageRemover;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Resolver\FileNameResolvers\OriginalFileNameResolver;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use Contributte\Imagist\Resolver\FilterResolvers\MD5FilterResolver;
use Contributte\Imagist\Resolver\FilterResolvers\OriginalFilterResolver;
use Contributte\Imagist\Resolver\FilterResolvers\SimpleFilterResolver;
use Contributte\Imagist\Storage\ImageStorage;
use Contributte\Imagist\Transaction\TransactionFactory;
use Imagine\Image\AbstractImagine;

final class LocalImageStorageBuilder
{

	private string $rootDir;

	private FilterProcessorInterface $filterProcessor;

	private FilterResolverInterface $filterResolver;

	public function __construct(string $rootDir)
	{
		$this->rootDir = $rootDir;

		$this->withOriginalFilterResolver();

		if (class_exists(AbstractImagine::class)) {
			$this->withImagineFilterProcessor();
		} else {
			$this->withNetteFilterProcessor();
		}
	}

	public function withNetteFilterProcessor(): self
	{
		$this->filterProcessor = new FilterProcessor(
			new NetteResourceFactory(),
			[new NetteOperationProcessor()]
		);

		return $this;
	}

	public function withImagineFilterProcessor(): self
	{
		$this->filterProcessor = new FilterProcessor(
			new ImagineResourceFactory(),
			[new ImagineOperationProcessor()]
		);

		return $this;
	}

	public function withSimpleFilterResolver(): self
	{
		$this->filterResolver = new SimpleFilterResolver();

		return $this;
	}

	public function withOriginalFilterResolver(): self
	{
		$this->filterResolver = new OriginalFilterResolver();

		return $this;
	}

	public function withMd5FilterResolver(): self
	{
		$this->filterResolver = new MD5FilterResolver();

		return $this;
	}

	public function build(): ImageStorageBuilderResult
	{
		$fileFactory = new FileFactory(
			$filesystem = new LocalFilesystem($this->rootDir),
			$pathInfoFactory = new PathInfoFactory($this->filterResolver)
		);

		$persister = new ChainImagePersister([
			new EmptyImagePersister(),
			new PersistentImagePersister($fileFactory, $this->filterProcessor),
			new StorableImagePersister($fileFactory, $this->filterProcessor, new OriginalFileNameResolver()),
		]);

		$remover = new ChainImageRemover([
			new EmptyImageRemover(),
			new PersistentImageRemover($fileFactory, $pathInfoFactory, $filesystem),
		]);

		$imageStorage = new ImageStorage($persister, $remover);
		$linkGenerator = new LinkGenerator($imageStorage, $fileFactory);
		$transactionFactory = new TransactionFactory($imageStorage, $fileFactory);

		return new ImageStorageBuilderResult($imageStorage, $linkGenerator, $transactionFactory);
	}

}
