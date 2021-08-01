<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Debugger\DebugFilterObject;
use Contributte\Imagist\Debugger\FilterDebuggerInterface;
use Tracy\IBarPanel;

final class FilterBarPanel implements IBarPanel
{

	private FilterDebuggerInterface $debugger;

	public function __construct(FilterDebuggerInterface $debugger)
	{
		$this->debugger = $debugger;
	}

	/**
	 * @return DebugFilterObject[]
	 */
	protected function getFilters(): array
	{
		return $this->debugger->getAll();
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
