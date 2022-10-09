<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Bridge\Nette\Tracy\Dto\BarEvent;
use Contributte\Imagist\Event\PersistedImageEvent;
use Contributte\Imagist\Event\RemovedImageEvent;
use LogicException;
use Tracy\IBarPanel;

final class ImageBarPanel implements IBarPanel
{

	/** @var BarEvent[] */
	private array $events = [];

	public function persistedEvent(PersistedImageEvent $event): void
	{
		$this->events[] = new BarEvent($event);
	}

	public function removedEvent(RemovedImageEvent $event): void
	{
		$this->events[] = new BarEvent($event);
	}

	public function getTab(): string
	{
		$html = file_get_contents(__DIR__ . '/assets/tab.html');

		return sprintf('%s (%d)', $html, count($this->events));
	}

	public function getPanel(): string
	{
		ob_start();

		// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$events = $this->events;
		// phpcs:enable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable

		require __DIR__ . '/assets/panel.phtml';

		$contents = ob_get_clean();
		if ($contents === false) {
			throw new LogicException('Something gone wrong');
		}

		return $contents;
	}

}
