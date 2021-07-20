<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\PersistentImageInterface;
use LogicException;

final class RemoverRegistry implements RemoverRegistryInterface
{

	/** @var RemoverInterface[] */
	private array $removers = [];

	public function add(RemoverInterface $remover): void
	{
		$this->removers[] = $remover;
	}

	public function remove(PersistentImageInterface $image, Context $context): void
	{
		foreach ($this->removers as $remover) {
			if ($remover->supports($image)) {
				$remover->remove($image);

				return;
			}
		}

		throw new LogicException(sprintf('Remover for class %s not found', get_class($image)));
	}

}
