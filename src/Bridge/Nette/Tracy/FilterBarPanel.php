<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Bridge\Nette\Tracy\Dto\DebugFilterDto;
use Contributte\Imagist\Config\ConfigFilter;
use Contributte\Imagist\Config\ConfigFilterStack;
use Tracy\IBarPanel;

final class FilterBarPanel implements IBarPanel
{

	/** @var DebugFilterDto[] */
	private array $debug = [];

	public function addConfigFilterStack(ConfigFilterStack $configFilterStack): void
	{
		$this->debug[] = new DebugFilterDto($configFilterStack->getName(), array_map(
			fn (ConfigFilter $f) => sprintf('%s(%s)', $f->getName(), implode(', ', $f->getArguments())),
			$configFilterStack->getConfigFilters(),
		));
	}

	public function getTab(): string
	{
		ob_start();

		require __DIR__ . '/assets/config.filter.tab.phtml';

		return ob_get_clean() ?: '';
	}

	public function getPanel(): string
	{
		ob_start();

		require __DIR__ . '/assets/config.filter.panel.phtml';

		return ob_get_clean() ?: '';
	}

}
