<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Filter\Context\ContextInterface;

final class ChainImagePersister implements PersisterInterface
{

	/** @var PersisterInterface[] */
	private array $persisters;

	/**
	 * @param PersisterInterface[] $persisters
	 */
	public function __construct(array $persisters)
	{
		$this->persisters = $persisters;
	}

	public function supports(ImageInterface $image, ContextInterface $context): bool
	{
		return true;
	}

	public function persist(ImageInterface $image, ContextInterface $context): ImageInterface
	{
		foreach ($this->persisters as $persister) {
			if ($persister->supports($image, $context)) {
				return $persister->persist($image, $context);
			}
		}

		throw new InvalidArgumentException(sprintf('Persister not found for class %s', $image::class));
	}

}
