<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Bridge\Doctrine\Event\PersisterEvent;
use Contributte\Imagist\Bridge\Doctrine\Event\RemoveEvent;
use Contributte\Imagist\Bridge\Doctrine\ImageType;
use Contributte\Imagist\Bridge\Gumlet\GumletLinkGenerator;
use Contributte\Imagist\Bridge\Imagine\FilterProcessor;
use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Bridge\Imagine\OperationRegistry;
use Contributte\Imagist\Bridge\Imagine\OperationRegistryInterface;
use Contributte\Imagist\Bridge\Nette\Latte\LatteImageProvider;
use Contributte\Imagist\Bridge\Nette\LinkGenerator;
use Contributte\Imagist\Bridge\Nette\Macro\ImageMacro;
use Contributte\Imagist\Bridge\Nette\Tracy\ImageBarPanel;
use Contributte\Imagist\Bridge\Nette\Tracy\ImagistBlueScreen;
use Contributte\Imagist\Bridge\NetteImage\NetteFilterProcessor;
use Contributte\Imagist\Bridge\NetteImage\NetteOperationRegistry;
use Contributte\Imagist\Bridge\NetteImage\NetteOperationRegistryInterface;
use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Database\DatabaseConverterInterface;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filesystem\FilesystemInterface;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterNormalizerCollection;
use Contributte\Imagist\Filter\FilterNormalizerCollectionInterface;
use Contributte\Imagist\Filter\FilterNormalizerInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\Filter\VoidFilterProcessor;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\LinkGenerator\LinkGenerator as LegacyLinkGenerator;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\PersisterInterface;
use Contributte\Imagist\Persister\PersisterRegistry;
use Contributte\Imagist\Persister\PersisterRegistryInterface;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Remover\RemoverInterface;
use Contributte\Imagist\Remover\RemoverRegistry;
use Contributte\Imagist\Remover\RemoverRegistryInterface;
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
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\AbstractImagine;
use LogicException;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nettrine\DBAL\DI\DbalExtension;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tracy\Bar;
use Tracy\BlueScreen;
use Tracy\IBarPanel;

final class ImageStorageExtension extends CompilerExtension
{

	/** @var callable[] */
	private array $onBeforeCompile = [];

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'extensions' => Expect::structure([
				'doctrine' => Expect::structure([
					'removeEvent' => Expect::bool(false),
					'promisedPersistEvent' => Expect::bool(false),
				]),
				'gumlet' => Expect::structure([
					'bucket' => Expect::string(),
					'token' => Expect::string()->nullable(),
				]),
				'nette' => Expect::structure([
					'filters' => Expect::structure([
						'enabled' => Expect::bool(false),
					]),
				]),
				'imagine' => Expect::structure([
					'enabled' => Expect::bool(class_exists(AbstractImagine::class)),
				]),
			]),
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
		$this->loadDebugger($builder);
		$this->loadPersister($builder);
		$this->loadRemover($builder);

		if ($config->extensions->nette->filters->enabled) {
			$this->loadNetteImageFilters($builder);
		} elseif ($config->extensions->imagine->enabled) {
			$this->loadImageFiltersExtension($builder);
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
		$this->loadGumlet($builder);
		$this->loadLatte($builder);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$this->injectNormalizers($builder);
		$this->injectRemovers($builder);
		$this->injectPersisters($builder);

		$serviceName = $builder->getByType(Bar::class);
		if ($serviceName) {
			if ($builder->hasDefinition($this->prefix('tracy.bar'))) {
				$this->assertServiceDefinition($builder->getDefinition($serviceName))
					->addSetup('addPanel', [$builder->getDefinition($this->prefix('tracy.bar'))]);
			}
		}

		$serviceName = $builder->getByType(BlueScreen::class);
		if ($serviceName) {
			$this->assertServiceDefinition($builder->getDefinition($serviceName))
				->addSetup('?::install(?);', [ImagistBlueScreen::class, '@self']);
		}

		$serviceName = $builder->getByType(EventDispatcherInterface::class, false);
		if ($serviceName) {
			$this->assertServiceDefinition($builder->getDefinition($serviceName))
				->addSetup('addSubscriber', [$builder->getDefinition($this->prefix('tracy.bar'))]);
		}

		foreach ($this->onBeforeCompile as $callback) {
			$callback();
		}
	}

	private function injectNormalizers(ContainerBuilder $builder): void
	{
		$service = $builder->getDefinition($this->prefix('filter.normalizerCollection'));
		assert($service instanceof ServiceDefinition);

		foreach ($builder->findByType(FilterNormalizerInterface::class) as $normalizer) {
			$service->addSetup('add', [$normalizer]);
		}
	}

	private function injectPersisters(ContainerBuilder $builder): void
	{
		$service = $builder->getDefinition($this->prefix('persisterRegistry'));
		assert($service instanceof ServiceDefinition);

		foreach ($builder->findByType(PersisterInterface::class) as $persister) {
			$service->addSetup('add', [$persister]);
		}
	}

	private function injectRemovers(ContainerBuilder $builder): void
	{
		$service = $builder->getDefinition($this->prefix('removerRegistry'));
		assert($service instanceof ServiceDefinition);

		foreach ($builder->findByType(RemoverInterface::class) as $remover) {
			$service->addSetup('add', [$remover]);
		}
	}

	private function loadFilesystem(ContainerBuilder $builder): void
	{
		if (!isset($builder->parameters['wwwDir'])) {
			throw new LogicException('Neon parameter %wwwDir% must be configured');
		}

		$builder->addDefinition($this->prefix('filesystem'))
			->setType(FilesystemInterface::class)
			->setFactory(LocalFilesystem::class, [
				$builder->parameters['wwwDir'],
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
			->setType(FilterNormalizerCollectionInterface::class)
			->setFactory(FilterNormalizerCollection::class);

		$builder->addDefinition($this->prefix('filterProcessor'))
			->setType(FilterProcessorInterface::class)
			->setFactory(VoidFilterProcessor::class);
	}

	private function loadImageFiltersExtension(ContainerBuilder $builder): void
	{
		if (!class_exists(AbstractImagine::class)) {
			return;
		}

		$this->assertServiceDefinition($builder->getDefinition($this->prefix('filterProcessor')))
			->setFactory(FilterProcessor::class);

		$registry = $builder->addDefinition($this->prefix('imagine.operationRegistry'))
			->setType(OperationRegistryInterface::class)
			->setFactory(OperationRegistry::class);

		$this->onBeforeCompile[] = fn () => $this->foreach(
			$builder->findByType(OperationInterface::class),
			fn (Definition $definition) => $registry->addSetup('add', [$definition])
		);
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

		$this->assertServiceDefinition($builder->getDefinition($serviceName))
			->addSetup('?::register(?)', [ImageType::class, '@self']);

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

	private function loadGumlet(ContainerBuilder $builder): void
	{
		/** @var stdClass $config */
		$config = $this->getConfig();
		$config = $config->extensions->gumlet;
		if (!$config->bucket) {
			return;
		}

		$builder->addDefinition($this->prefix('extensions.gumlet.linkGenerator'))
			->setFactory(GumletLinkGenerator::class, [$config->bucket, $config->token]);

		$builder->getDefinition($this->prefix('linkGenerator'))
			->setAutowired(false);
	}

	private function loadLatte(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(ILatteFactory::class);
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

	private function loadDebugger(ContainerBuilder $builder): void
	{
		if (!interface_exists(EventSubscriberInterface::class)) {
			return;
		}

		$builder->addDefinition($this->prefix('tracy.bar'))
			->setType(IBarPanel::class)
			->setFactory(ImageBarPanel::class)
			->setAutowired(false);
	}

	private function loadPersister(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('persisterRegistry'))
			->setType(PersisterRegistryInterface::class)
			->setFactory(PersisterRegistry::class);

		$builder->addDefinition($this->prefix('persisters.emptyImage'))
			->setType(PersisterInterface::class)
			->setFactory(EmptyImagePersister::class);

		$builder->addDefinition($this->prefix('persisters.storableImage'))
			->setType(PersisterInterface::class)
			->setFactory(StorableImagePersister::class);

		$builder->addDefinition($this->prefix('persisters.persistentImage'))
			->setType(PersisterInterface::class)
			->setFactory(PersistentImagePersister::class);
	}

	private function loadRemover(ContainerBuilder $builder): void
	{
		$builder->addDefinition($this->prefix('removerRegistry'))
			->setType(RemoverRegistryInterface::class)
			->setFactory(RemoverRegistry::class);

		$builder->addDefinition($this->prefix('removers.emptyImage'))
			->setType(RemoverInterface::class)
			->setFactory(EmptyImageRemover::class);

		$builder->addDefinition($this->prefix('removers.persisterImage'))
			->setType(RemoverInterface::class)
			->setFactory(PersistentImageRemover::class);
	}

	private function loadNetteImageFilters(ContainerBuilder $builder): void
	{
		$this->assertServiceDefinition($builder->getDefinition($this->prefix('filterProcessor')))
			->setFactory(NetteFilterProcessor::class);

		$registry = $builder->addDefinition($this->prefix('nette.filters.operationRegistry'))
			->setType(NetteOperationRegistryInterface::class)
			->setFactory(NetteOperationRegistry::class);

		$this->onBeforeCompile[] = fn () => $this->foreach(
			$builder->findByType(OperationInterface::class),
			fn (Definition $operation) => $registry->addSetup('add', [$operation])
		);
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
