<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Filter\Context\ContextInterface;

final class PersisterRegistry implements PersisterRegistryInterface
{

	/** @var PersisterInterface[] */
	private array $persisters = [];

	public function add(PersisterInterface $persister): void
	{
		$this->persisters[] = $persister;
	}

	public function persist(ImageInterface $image, ContextInterface $context): ImageInterface
	{
		foreach ($this->persisters as $persister) {
			if ($persister->supports($image, $context)) {
				return $persister->persist($image, $context);
			}
		}

		throw new InvalidArgumentException(sprintf('Persist not found for class %s', get_class($image)));
	}

}
