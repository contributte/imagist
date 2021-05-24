<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Bridge\Imagine\Config\ImagineConfigFilterRegistry;
use Contributte\Imagist\Bridge\Imagine\Config\ImagineConfigOperationInterface;
use Contributte\Imagist\Bridge\Imagine\Config\Operation as ImagineOperation;
use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigFilterRegistry;
use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;
use Contributte\Imagist\Bridge\Nette\Filter\Config\Operation as NetteOperation;
use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationInterface;
use Contributte\Imagist\Bridge\Nette\Tracy\FilterBarPanel;
use Contributte\Imagist\Config\ConfigFilter;
use Contributte\Imagist\Config\ConfigFilterStack;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Tracy\Bar;

final class ImageStorageConfigFiltersExtension extends CompilerExtension
{

	private const IMAGINE_OPERATIONS = [
		'resize' => ImagineOperation\ResizeConfigOperation::class,
	];

	private const NETTE_OPERATIONS = [
		'resize' => NetteOperation\ResizeConfigOperation::class,
		'sharpen' => NetteOperation\SharpenConfigOperation::class,
		'crop' => NetteOperation\CropConfigOperation::class,
		'flip' => NetteOperation\FlipConfigOperation::class,
	];

	private bool $tracyBar;

	private ?ServiceDefinition $tracy = null;

	public function __construct(bool $tracyBar = true)
	{
		$this->tracyBar = $tracyBar;
	}

	public function getConfigSchema(): Schema
	{
		return Expect::arrayOf(Expect::anyOf(Expect::type(Statement::class), Expect::listOf(Statement::class)));
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('nette.registry'))
			->setType(NetteOperationInterface::class)
			->setFactory(NetteConfigFilterRegistry::class);

		$builder->addDefinition($this->prefix('imagine.registry'))
			->setType(OperationInterface::class)
			->setFactory(ImagineConfigFilterRegistry::class);

		if ($this->tracyBar) {
			$this->tracy = $builder->addDefinition($this->prefix('tracy'))
				->setFactory(FilterBarPanel::class);
		}

		foreach (self::NETTE_OPERATIONS as $name => $class) {
			$builder->addDefinition($this->prefix('nette.operation.' . $name))
				->setType(NetteConfigOperationInterface::class)
				->setFactory($class);
		}

		foreach (self::IMAGINE_OPERATIONS as $name => $class) {
			$builder->addDefinition($this->prefix('imagine.operation.' . $name))
				->setType(ImagineConfigOperationInterface::class)
				->setFactory($class);
		}
	}

	public function beforeCompile()
	{
		/** @var mixed[] $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		if ($builder->hasDefinition($this->prefix('tracy')) && ($service = $builder->getByType(Bar::class))) {
			$service = $builder->getDefinition($service);
			assert($service instanceof ServiceDefinition);

			$service->addSetup('addPanel', [$builder->getDefinition($this->prefix('tracy'))]);
		}

		$this->processImagineOperations($builder);
		$this->processNetteOperations($builder);

		$this->processConfigFilters($builder, $config);
	}

	private function processImagineOperations(ContainerBuilder $builder): void
	{
		$service = $builder->getDefinition($this->prefix('imagine.registry'));
		assert($service instanceof ServiceDefinition);

		foreach ($builder->findByType(ImagineConfigOperationInterface::class) as $definition) {
			$service->addSetup('addOperation', [$definition]);
		}
	}

	private function processNetteOperations(ContainerBuilder $builder): void
	{
		$service = $builder->getDefinition($this->prefix('nette.registry'));
		assert($service instanceof ServiceDefinition);

		foreach ($builder->findByType(NetteConfigOperationInterface::class) as $definition) {
			$service->addSetup('addOperation', [$definition]);
		}
	}

	/**
	 * @param mixed[] $config
	 */
	private function processConfigFilters(ContainerBuilder $builder, array $config): void
	{
		/** @var ServiceDefinition[] $registries */
		$registries = [
			$builder->getDefinition($this->prefix('nette.registry')),
			$builder->getDefinition($this->prefix('imagine.registry')),
		];

		foreach ($config as $name => $filters) {
			if (!is_array($filters)) {
				$filters = [$filters];
			}

			$stack = [];
			foreach ($filters as $filter) {
				$stack[] = new Statement(ConfigFilter::class, [$filter->getEntity(), $filter->arguments]);
			}

			$arguments = [new Statement(ConfigFilterStack::class, [$name, $stack])];

			foreach ($registries as $registry) {
				$registry->addSetup('addConfigFilterStack', $arguments);
			}

			if ($this->tracy) {
				$this->tracy->addSetup('addConfigFilterStack', $arguments);
			}
		}
	}

}
