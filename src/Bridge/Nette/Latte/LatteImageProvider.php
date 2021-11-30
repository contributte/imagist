<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Latte;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollectionInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use LogicException;
use Nette\Utils\Arrays;

final class LatteImageProvider
{

	private LinkGeneratorInterface $linkGenerator;

	private ?StringFilterCollectionInterface $stringFilterCollection;

	public function __construct(
		LinkGeneratorInterface $linkGenerator,
		?StringFilterCollectionInterface $stringFilterCollection = null
	)
	{
		$this->linkGenerator = $linkGenerator;
		$this->stringFilterCollection = $stringFilterCollection;
	}

	/**
	 * @param string|PersistentImageInterface|null $id
	 * @param mixed[] $options
	 */
	public function link($id, array $options = []): ?string
	{
		if (is_string($id)) {
			$image = new PersistentImage($id);
		} elseif ($id === null) {
			$image = new EmptyImage();
		} else {
			$image = $id;
		}

		$filter = $options['filter'] ?? null;
		unset($options['filter']);

		if ($filter !== null) {
			if ($filter instanceof FilterInterface) {
				$image = $image->withFilter($filter);
			} else if (is_string($filter)) {
				if (!$this->stringFilterCollection) {
					throw new LogicException(
						sprintf(
							'Class %s have to be set if you want use string filters.',
							StringFilterCollectionInterface::class
						)
					);
				}

				$image = $image->withFilter($this->stringFilterCollection->get($filter));
			} else if (is_array($filter)) {
				if (!$this->stringFilterCollection) {
					throw new LogicException(
						sprintf(
							'Class %s have to be set if you want use string filters.',
							StringFilterCollectionInterface::class
						)
					);
				}

				$first = Arrays::first($filter);

				if (!$first) {
					throw new LogicException('Filter cannot be an empty array.');
				}

				$image = $image->withFilter($this->stringFilterCollection->get($first, array_slice($filter, 1)));
			} else {
				throw new LogicException(
					sprintf(
						'Filter have to be a string or an array or an object instance of %s, %s given.',
						FilterInterface::class,
						get_debug_type($filter)
					)
				);
			}
		}

		return $this->linkGenerator->link($image, $options);
	}

}
