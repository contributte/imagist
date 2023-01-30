<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Bridge\Doctrine\Event\PersisterEvent;
use Contributte\Imagist\Bridge\Doctrine\Event\RemoveEvent;
use Contributte\Imagist\Bridge\Doctrine\ImageType;
use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationProcessor;
use Contributte\Imagist\Bridge\Nette\Filter\NetteResourceFactory;
use Contributte\Imagist\Bridge\Nette\Latte\LatteImageProvider;
use Contributte\Imagist\Bridge\Nette\LinkGenerator;
use Contributte\Imagist\Bridge\Nette\Macro\ImageMacro;
use Contributte\Imagist\Bridge\Nette\Tracy\ImageBarPanel;
use Contributte\Imagist\Bridge\Nette\Tracy\ImagistBlueScreen;
use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Database\DatabaseConverterInterface;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filesystem\FilesystemInterface;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterNormalizer;
use Contributte\Imagist\Filter\FilterNormalizerInterface;
use Contributte\Imagist\Filter\FilterProcessor;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Resource\ResourceFactoryInterface;
use Contributte\Imagist\Filter\VoidFilterProcessor;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\LinkGenerator\LinkGenerator as LegacyLinkGenerator;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Persister\ChainImagePersister;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\PersisterInterface;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\ChainImageRemover;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Remover\RemoverInterface;
use Contributte\Imagist\Resolver\BucketResolverInterface;
use Contributte\Imagist\Resolver\BucketResolvers\BucketResolver;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;
use Contributte\Imagist\Resolver\DefaultImageResolvers\NullDefaultImageResolver;
use Contributte\Imagist\Resolver\FileNameResolverInterface;
use Contributte\Imagist\Resolver\FileNameResolvers\PrefixFileNameResolver;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use Contributte\Imagist\Resolver\FilterResolvers\OriginalFilterResolver;
use Contributte\Imagist\Storage\ImageStorage;
use Contributte\Imagist\Transaction\TransactionFactory;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\MissingServiceException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\Arrays;
use Nettrine\DBAL\DI\DbalExtension;
use stdClass;
use Tracy\Bar;
use Tracy\BlueScreen;

/**
 * @final
 */
/*final*/ class ImagistExtension extends CompilerExtension
{

	/** @var callable[] */
	private array $onBeforeCompile = [];

	public function getConfigSchema(): Schema
	{
		$builder = $this->getContainerBuilder();

		return Expect::structure([
			'extensions' => Expect::structure([
				'doctrine' => Expect::structure([
					'removeEvent' => Expect::bool(false),
					'persistEvent' => Expect::bool(false),
					'types' => Expect::listOf(Expect::structure([
						'name' => Expect::string()->required(),
						'databaseName' => Expect::string()->required(),
						'class' => Expect::string()->required(),
					])->castTo('array')),
				]),
				'nette' => Expect::structure([
					'filters' => Expect::structure([
						'enabled' => Expect::bool(true),
					]),
				]),
				'tracy' => Expect::structure([
					'tabWithName' => Expect::bool(false),
				]),
				'imagine' => Expect::structure([
					'enabled' => Expect::bool(false),
				]),
			]),
			'registration' => Expect::structure([
				'persisters' => Expect::bool(true),
				'removers' => Expect::bool(true),
			]),
			'baseDir' => Expect::string($builder->parameters['wwwDir']),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		$this->loadFilesystem($builder);
		$this->loadResolvers($builder);
		$this->loadPathInfo($builder);
		$this->loadFile($builder);
		$this->loadDatabase($builder);
		$this->loadFilter($builder);

		if ($config->registration->persisters) {
			$this->loadPersister($builder);
		}

		if ($config->registration->removers) {
			$this->loadRemover($builder);
		}

		if ($config->extensions->nette->filters->enabled) {
			$this->loadNette($builder);
		}

		if ($config->extensions->imagine->enabled) {
			$this->loadImagine($builder);
		}

		$builder->addDefinition($this->prefix('storage'))
			->setType(ImageStorageInterface::class)
			->setFactory(ImageStorage::class);

		$legacy = $builder->addDefinition($this->prefix('legacyLinkGenerator'))
			->setType(LinkGeneratorInterface::class)
			->setFactory(LegacyLinkGenerator::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('linkGenerator'))
			->setType(LinkGeneratorInterface::class)
			->setFactory(LinkGenerator::class, [$legacy]);

		$builder->addDefinition($this->prefix('transactionFactory'))
			->setType(TransactionFactoryInterface::class)
			->setFactory(TransactionFactory::class);

		// extensions
		$this->loadDoctrine($builder);
		$this->loadLatte($builder);
		$this->loadTracy($builder);
	}

	private function loadTracy(ContainerBuilder $builder): void
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		$panel = $builder->addDefinition($this->prefix('tracy.bar'))
			->setFactory(ImageBarPanel::class, [$config->extensions->tracy->tabWithName]);

		$this->onBeforeCompile[] = function () use ($panel): void {
			$builder = $this->getContainerBuilder();

			try {
				$bar = $this->assertServiceDefinition(
					$builder->getDefinitionByType(Bar::class)
				);
			} catch (MissingServiceException $e) {
				return;
			}

			$bar->addSetup('addPanel', [$panel]);

			$imageStorage = $builder->getDefinition($this->prefix('storage'));

			if ($imageStorage instanceof ServiceDefinition && $imageStorage->getEntity() === ImageStorage::class) {
				$imageStorage->addSetup('?->onPersist[] = [?, "persistedEvent"]', ['@self', $panel]);
				$imageStorage->addSetup('?->onRemove[] = [?, "removedEvent"]', ['@self', $panel]);
			}
		};
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		Arrays::invoke($this->onBeforeCompile);

		try {
			$this->assertServiceDefinition($builder->getDefinitionByType(BlueScreen::class))
				->addSetup('?::install(?);', [ImagistBlueScreen::class, '@self']);
		} catch (MissingServiceException $e) {
			// no need
		}
	}

	private function loadFilesystem(ContainerBuilder $builder): void
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		$builder->addDefinition($this->prefix('filesystem'))
			->setType(FilesystemInterface::class)
			->setFactory(LocalFilesystem::class, [
				$config->baseDir,
			]);
	}

	private function loadResolvers(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('resolvers.bucket'))
			->setType(BucketResolverInterface::class)
			->setFactory(BucketResolver::class);

		$builder->addDefinition($this->prefix('resolvers.fileName'))
			->setType(FileNameResolverInterface::class)
			->setFactory(PrefixFileNameResolver::class);

		$builder->addDefinition($this->prefix('resolvers.filter'))
			->setType(FilterResolverInterface::class)
			->setFactory(OriginalFilterResolver::class);

		$builder->addDefinition($this->prefix('resolvers.defaultImage'))
			->setType(DefaultImageResolverInterface::class)
			->setFactory(NullDefaultImageResolver::class);
	}

	private function loadPathInfo(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('pathInfoFactory'))
			->setType(PathInfoFactoryInterface::class)
			->setFactory(PathInfoFactory::class);
	}

	private function loadFile(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('fileFactory'))
			->setType(FileFactoryInterface::class)
			->setFactory(FileFactory::class);
	}

	private function loadDatabase(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('database.converter'))
			->setType(DatabaseConverterInterface::class)
			->setFactory(DatabaseConverter::class);
	}

	private function loadFilter(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('filter.normalizerCollection'))
			->setType(FilterNormalizerInterface::class)
			->setFactory(FilterNormalizer::class);

		$builder->addDefinition($this->prefix('filterProcessor'))
			->setType(FilterProcessorInterface::class)
			->setFactory(VoidFilterProcessor::class);
	}

	private function loadImagine(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('imagine.resource.factory'))
			->setType(ResourceFactoryInterface::class)
			->setFactory(ImagineResourceFactory::class);

		$builder->addDefinition($this->prefix('imagine.operation.processor'))
			->setType(OperationProcessorInterface::class)
			->setFactory(ImagineOperationProcessor::class);

		$this->assertServiceDefinition(
			$builder->getDefinition($this->prefix('filterProcessor'))
		)->setFactory(FilterProcessor::class);
	}

	private function loadDoctrine(ContainerBuilder $builder): void
	{
		if (!interface_exists(Reader::class)) {
			return;
		}

		/** @var stdClass $config */
		$config = $this->getConfig();
		$serviceName = $builder->getByType(Connection::class);
		if (!$serviceName) {
			return;
		}

		/** @var array{class: string, name: string, databaseName: string}[] $types */
		$types = array_merge([
			['class' => PersistentImage::class, 'name' => 'image', 'databaseName' => 'db_image'],
		], $config->extensions->doctrine->types);

		foreach ($types as $type) {
			$this->assertServiceDefinition($builder->getDefinition($serviceName))
				->addSetup(
					'?::register(?, ?, ?, ?)',
					[ImageType::class, '@self', $type['name'], $type['databaseName'], $type['class']]
				);
		}

		$autoRegistration = (bool) $this->compiler->getExtensions(DbalExtension::class);
		if ($config->extensions->doctrine->removeEvent) {
			$service = $builder->addDefinition($this->prefix('doctrine.events.remove'))
				->setFactory(RemoveEvent::class)
				->setAutowired(false);

			if (!$autoRegistration) {
				$this->assertServiceDefinition($builder->getDefinitionByType(EntityManagerInterface::class))
					->addSetup('?->getEventManager()->addEventSubscriber(?);', ['@self', $service]);
			}
		}

		if ($config->extensions->doctrine->promisedPersistEvent) {
			$service = $builder->addDefinition($this->prefix('doctrine.events.promisedPersist'))
				->setFactory(PersisterEvent::class)
				->setAutowired(false);

			if (!$autoRegistration) {
				$this->assertServiceDefinition($builder->getDefinitionByType(EntityManagerInterface::class))
					->addSetup('?->getEventManager()->addEventSubscriber(?);', ['@self', $service]);
			}
		}
	}

	private function loadLatte(ContainerBuilder $builder): void
	{
		if (version_compare(Engine::VERSION, '3', '<')) { // @phpstan-ignore-line
			$this->loadLatte2($builder);
		} else {
			$this->loadLatte3($builder);
		}
	}

	private function loadLatte2(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$builder->addDefinition($this->prefix('latte.provider'))
			->setFactory(LatteImageProvider::class);

		$factory = $builder->getDefinition($serviceName);
		assert($factory instanceof FactoryDefinition);

		$factory->getResultDefinition()
			->addSetup('?->onCompile[] = function ($engine) { ?::install($engine->getCompiler()); }', [
				'@self',
				ImageMacro::class,
			])
			->addSetup('addProvider', ['images', $this->prefix('@latte.provider')]);
	}

	private function loadLatte3(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$builder->addDefinition($this->prefix('latte.provider'))
			->setFactory(LatteImageProvider::class);

		$extension = $builder->addDefinition($this->prefix('latte.extension'))
			->setFactory(\Contributte\Imagist\Bridge\Nette\Latte\Extension\ImagistExtension::class);

		$factory = $builder->getDefinition($serviceName);
		assert($factory instanceof FactoryDefinition);

		$factory->getResultDefinition()
			->addSetup('addExtension', [$extension]);
	}

	private function loadPersister(ContainerBuilder $builder): void
	{
		$chain = [];
		$chain[] = $builder->addDefinition($this->prefix('persisters.emptyImage'))
			->setType(PersisterInterface::class)
			->setFactory(EmptyImagePersister::class)
			->setAutowired(false);

		$chain[] = $builder->addDefinition($this->prefix('persisters.storableImage'))
			->setType(PersisterInterface::class)
			->setFactory(StorableImagePersister::class)
			->setAutowired(false);

		$chain[] = $builder->addDefinition($this->prefix('persisters.persistentImage'))
			->setType(PersisterInterface::class)
			->setFactory(PersistentImagePersister::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('persisterRegistry'))
			->setType(PersisterInterface::class)
			->setFactory(ChainImagePersister::class, [$chain]);
	}

	private function loadRemover(ContainerBuilder $builder): void
	{
		$chain = [];
		$chain[] = $builder->addDefinition($this->prefix('removers.emptyImage'))
			->setType(RemoverInterface::class)
			->setFactory(EmptyImageRemover::class)
			->setAutowired(false);

		$chain[] = $builder->addDefinition($this->prefix('removers.persisterImage'))
			->setType(RemoverInterface::class)
			->setFactory(PersistentImageRemover::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('removerRegistry'))
			->setType(RemoverInterface::class)
			->setFactory(ChainImageRemover::class, [$chain]);
	}

	private function loadNette(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('nette.resource.factory'))
			->setType(ResourceFactoryInterface::class)
			->setFactory(NetteResourceFactory::class);

		$builder->addDefinition($this->prefix('nette.operation.processor'))
			->setType(OperationProcessorInterface::class)
			->setFactory(NetteOperationProcessor::class);

		$this->assertServiceDefinition(
			$builder->getDefinition($this->prefix('filterProcessor'))
		)->setFactory(FilterProcessor::class);
	}

	private function assertServiceDefinition(Definition $definition): ServiceDefinition
	{
		assert($definition instanceof ServiceDefinition);

		return $definition;
	}

	/**
	 * @param mixed[] $array
	 */
	private function foreach(array $array, callable $func): void
	{
		foreach ($array as $key => $value) {
			$func($value, $key);
		}
	}

}
