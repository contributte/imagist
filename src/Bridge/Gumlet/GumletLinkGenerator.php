<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\Context\ContextFactory;
use Contributte\Imagist\Filter\Context\ContextFactoryInterface;
use Contributte\Imagist\Filter\FilterNormalizerInterface;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollectionInterface;
use Contributte\Imagist\Filter\StringFilter\StringFilterFacade;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;
use InvalidArgumentException;
use Typertion\Php\TypeAssert;

final class GumletLinkGenerator implements LinkGeneratorInterface
{

	public const DEFAULT_DOMAIN = 'gumlet.io';
	public const GUMLET_CONTEXT_KEY = 'gumlet';

	private string $domain = self::DEFAULT_DOMAIN;

	private ?string $bucket;

	private ?string $token;

	private ?string $customDomain;

	private PathInfoFactoryInterface $pathInfoFactory;

	private DefaultImageResolverInterface $defaultImageResolver;

	private FilterNormalizerInterface $filterNormalizer;

	private ?StringFilterCollectionInterface $stringFilterCollection;

	private ContextFactoryInterface $contextFactory;

	public function __construct(
		?string $bucket,
		?string $token,
		?string $customDomain,
		PathInfoFactoryInterface $pathInfoFactory,
		DefaultImageResolverInterface $defaultImageResolver,
		FilterNormalizerInterface $filterNormalizer,
		?StringFilterCollectionInterface $stringFilterCollection = null,
		?ContextFactoryInterface $contextFactory = null
	)
	{
		$this->bucket = $bucket;
		$this->token = $token;
		$this->customDomain = $customDomain;
		$this->pathInfoFactory = $pathInfoFactory;
		$this->defaultImageResolver = $defaultImageResolver;
		$this->filterNormalizer = $filterNormalizer;
		$this->stringFilterCollection = $stringFilterCollection;
		$this->contextFactory = $contextFactory ?? new ContextFactory();

		if (!$this->bucket && !$this->customDomain) {
			throw new InvalidArgumentException('Bucket or customDomain must be set.');
		}
	}

	public function setDomain(string $domain): void
	{
		$this->domain = $domain;
	}

	/**
	 * @param mixed[] $options
	 */
	public function link(?PersistentImageInterface $image, array $options = []): ?string
	{
		$image = StringFilterFacade::resolveByNullableImage($this->stringFilterCollection, $image);

		if (!$image || $image instanceof EmptyImageInterface) {
			return $this->defaultImageResolver->resolve($this, $image, $options);
		}

		$domain = $this->customDomain;
		if (!$domain) {
			$domain = sprintf('%s.%s', $this->bucket, $this->domain);
		}

		return sprintf('https://%s/%s', $domain, $this->createPath($image, $options));
	}

	/**
	 * @param mixed[] $options
	 */
	private function createPath(PersistentImageInterface $image, array $options): string
	{
		$pathInfo = $this->pathInfoFactory->create($image->getOriginal());
		$path = $pathInfo->toString($pathInfo::BUCKET | $pathInfo::SCOPE | $pathInfo::IMAGE);

		$query = $this->filterNormalizer->normalize($image, $this->contextFactory->create([
			self::GUMLET_CONTEXT_KEY => true,
		]));

		$addons = isset($options['addons']) ? TypeAssert::array($options['addons']) : [];

		$query = array_merge($addons, $query);

		if (!$query) {
			return $this->hashIfNeed($path, true);
		}

		return $this->hashIfNeed($path . '?' . http_build_query($query), false);
	}

	private function hashIfNeed(string $path, bool $questionMark): string
	{
		if (!$this->token) {
			return $path;
		}

		return $path . ($questionMark ? '?' : '&') . 's=' . md5($this->token . '/' . $path);
	}

}
