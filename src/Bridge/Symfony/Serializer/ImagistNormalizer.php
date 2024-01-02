<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Serializer;

use ArrayObject;
use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ImagistNormalizer implements NormalizerInterface
{

	public const AS_ID = 'imagist.asId';
	public const AS_OBJECT = 'imagist.asObject';
	public const FILTERS = 'imagist.filters';
	public const FILTERS_BY_SCOPE = 'imagist.filtersByScope';
	public const FILTERS_BY_UNIQUE_ID = 'imagist.filtersByUniqueId';
	public const UNIQUE_ID = 'imagist.uniqueId';

	private LinkGeneratorInterface $linkGenerator;

	public function __construct(LinkGeneratorInterface $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param mixed[] $context
	 * @return mixed
	 */
	public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
	{
		assert($object instanceof PersistentImageInterface);

		if (($context[self::AS_OBJECT] ?? false) === true) {
			return $object;
		}

		if (($context[self::AS_ID] ?? false) === true) {
			if ($object instanceof EmptyImageInterface) {
				return null;
			}

			return $object->getId();
		}

		$filters = $context[self::FILTERS] ?? [];

		if ($filters) {
			return $this->applyFilters($object, $filters);
		}

		$filters = ($context[self::FILTERS_BY_SCOPE] ?? [])[$object->getScope()->toString()] ?? [];

		if ($filters) {
			return $this->applyFilters($object, $filters);
		}

		if (isset($context[self::UNIQUE_ID])) {
			$filters = ($context[self::FILTERS_BY_UNIQUE_ID] ?? [])[$context[self::UNIQUE_ID]] ?? [];

			if ($filters) {
				return $this->applyFilters($object, $filters);
			}
		}

		if ($object instanceof EmptyImageInterface) {
			return null;
		}

		return $this->linkGenerator->link($object);
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof PersistentImageInterface;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSupportedTypes(?string $format): array
	{
		return ['*' => true];
	}

	/**
	 * @param array<string, FilterInterface> $filters
	 * @return array<string|null>
	 */
	private function applyFilters(PersistentImageInterface $image, array $filters): array
	{
		$links = [];

		foreach ($filters as $name => $filter) {
			$links[$name] = $this->linkGenerator->link($image->withFilter($filter));
		}

		return $links;
	}

}
