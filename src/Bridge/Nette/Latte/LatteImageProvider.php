<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Latte;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use InvalidArgumentException;

final class LatteImageProvider
{

	private LinkGeneratorInterface $linkGenerator;

	public function __construct(LinkGeneratorInterface $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param string|PersistentImageInterface|null $id
	 * @param mixed[] $options
	 * @param mixed[] $filters
	 */
	public function link($id, array $options, array $filters): ?string
	{
		if (is_string($id)) {
			$image = new PersistentImage($id);
		} elseif ($id === null) {
			$image = new EmptyImage();
		} elseif ($id instanceof PersistentImageInterface) {
			$image = $id;
		} else {
			throw new InvalidArgumentException(
				sprintf(
					'First argument must be instance of %s or string or null, %s given',
					PersistentImageInterface::class,
					is_object($id) ? get_class($id) : gettype($id)
				)
			);
		}

		if (count($filters) > 1) {
			throw new InvalidArgumentException('Cannot use now two or more filters.');
		}

		foreach ($filters as $name => $opts) {
			if (is_numeric($name)) {
				$name = $opts;
				$opts = [];
			}

			$image = $image->withFilter($name, $opts);
		}

		return $this->linkGenerator->link($image, $options);
	}

}
