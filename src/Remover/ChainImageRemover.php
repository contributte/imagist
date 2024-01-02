<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Filter\Context\ContextInterface;

final class ChainImageRemover implements RemoverInterface
{

	/** @var RemoverInterface[] */
	private array $removers;

	/**
	 * @param RemoverInterface[] $removers
	 */
	public function __construct(array $removers)
	{
		$this->removers = $removers;
	}

	public function supports(PersistentImageInterface $image, ContextInterface $context): bool
	{
		return true;
	}

	public function remove(PersistentImageInterface $image, ContextInterface $context): void
	{
		foreach ($this->removers as $remover) {
			if ($remover->supports($image, $context)) {
				$remover->remove($image, $context);

				return;
			}
		}

		throw new InvalidArgumentException(sprintf('Remover not found for class %s', $image::class));
	}

}
