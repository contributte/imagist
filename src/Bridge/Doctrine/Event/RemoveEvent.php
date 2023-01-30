<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

final class RemoveEvent implements EventSubscriber
{

	private TransactionFactoryInterface $transactionFactory;

	public function __construct(TransactionFactoryInterface $transactionFactory)
	{
		$this->transactionFactory = $transactionFactory;
	}

	/**
	 * @return mixed[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::preRemove,
		];
	}

	public function preRemove(LifecycleEventArgs $args): void // @phpstan-ignore-line -- doctrine v2 does not have PreRemoveEventArgs
	{
		$object = $args->getObject();

		if ($object instanceof DoctrineImageRemover) {
			$this->removeImages($object->_imagesToRemove());
		} elseif ($object instanceof EntityImages) {
			$this->removeImages($object->_imagesToProcess());
		}
	}

	/**
	 * @param array<PersistentImageInterface|null> $images
	 */
	private function removeImages(array $images): void
	{
		if (!$images) {
			return;
		}

		$transaction = $this->transactionFactory->create();

		foreach ($images as $image) {
			if ($image && !$image->isEmpty()) {
				$transaction->remove($image);
			}
		}

		$transaction->commit();
	}

}
