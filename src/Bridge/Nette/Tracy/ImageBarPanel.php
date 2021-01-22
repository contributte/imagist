<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Bridge\Nette\Tracy\Dto\BarEvent;
use Contributte\Imagist\Event\PersistedImageEvent;
use Contributte\Imagist\Event\RemovedImageEvent;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tracy\IBarPanel;

final class ImageBarPanel implements IBarPanel, EventSubscriberInterface
{

	/** @var BarEvent[] */
	private array $events = [];

	/**
	 * @return string[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			PersistedImageEvent::class => 'persistedEvent',
			RemovedImageEvent::class => 'removedEvent',
		];
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
		$html = file_get_contents(__DIR__ . '/assets/tab.html');

		return sprintf('%s (%d)', $html, count($this->events));
	}

	public function getPanel(): string
	{
		ob_start();

		$events = $this->events;
		require __DIR__ . '/assets/panel.phtml';

		$contents = ob_get_clean();
		if ($contents === false) {
			throw new LogicException('Something gone wrong');
		}

		return $contents;
	}

}
