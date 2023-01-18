<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Bridge\Nette\Tracy\Dto\BarEvent;
use Contributte\Imagist\Event\PersistedImageEvent;
use Contributte\Imagist\Event\RemovedImageEvent;
use Nette\Utils\Helpers;
use Tracy\IBarPanel;

final class ImageBarPanel implements IBarPanel
{

	/** @var BarEvent[] */
	private array $events = []; // @phpstan-ignore-line -- Used in phtml

	private bool $tabWithName; // @phpstan-ignore-line -- Used in phtml

	public function __construct(bool $tabWithName = false)
	{
		$this->tabWithName = $tabWithName;
	}

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
		return Helpers::capture(function (): void {
			require __DIR__ . '/assets/tab.phtml';
		});
	}

	public function getPanel(): string
	{
		return Helpers::capture(function (): void {
			require __DIR__ . '/assets/panel.phtml';
		});
	}

}
