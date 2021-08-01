<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Nette\Http\IRequest;

final class LinkGenerator implements LinkGeneratorInterface
{

	private LinkGeneratorInterface $decorated;

	private string $baseUrl;

	public function __construct(LinkGeneratorInterface $decorated, IRequest $request)
	{
		$this->decorated = $decorated;
		$this->baseUrl = rtrim($request->getUrl()->getBaseUrl(), '/');
	}

	/**
	 * @inheritDoc
	 */
	public function link(?PersistentImageInterface $image, array $options = []): ?string
	{
		$path = $this->decorated->link($image, $options);
		if (!$path) {
			return null;
		}

		if (strncmp($path, 'http', strlen('http')) === 0) {
			return $path;
		}

		return $this->baseUrl . $path;
	}

}
