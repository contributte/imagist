<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\PromisedImageInterface;
use Contributte\Imagist\Transaction\TransactionInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class PersisterEvent implements EventSubscriber
{

	/**
	 * @return mixed[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::prePersist => 'prePersist',
			Events::preUpdate => 'preUpdate',
		];
	}

	public function preUpdate(PreUpdateEventArgs $args): void
	{
		$this->prePersist($args);
	}

	public function prePersist(LifecycleEventArgs $args): void // @phpstan-ignore-line -- doctrine v2 does not have PrePersistEventArgs
	{
		$object = $args->getObject();

		if ($object instanceof PromisedImagePersister) {
			$this->persistImages($object->_promisedImagesToPersist());
		} elseif ($object instanceof EntityImages) {
			$this->persistImages($object->_imagesToProcess());
		}
	}

	private function getTransaction(PersistentImageInterface $image): ?TransactionInterface
	{
		if ($image instanceof PromisedImageInterface && $image->isPending()) {
			return $image->getTransaction();
		}

		return null;
	}

	/**
	 * @param array<PersistentImageInterface|null> $images
	 */
	private function persistImages(array $images): void
	{
		if (!$images) {
			return;
		}

		foreach ($images as $image) {
			if (!$image) {
				continue;
			}

			$transaction = $this->getTransaction($image);

			if ($transaction && $transaction->isCommitted() === false) {
				$transaction->commit();
			}
		}
	}

}
