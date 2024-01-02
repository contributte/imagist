<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Exceptions\EmptyImageException;
use Contributte\Imagist\Scope\Scope;

class EmptyImage extends Image implements EmptyImageInterface
{

	public function __construct(?Scope $scope = null)
	{
		$this->scope = $scope ?? new Scope();
	}

	public function getId(): string
	{
		throw new EmptyImageException(sprintf('Cannot call %s on empty image', __METHOD__));
	}

	public function getName(): string
	{
		throw new EmptyImageException(sprintf('Cannot call %s on empty image', __METHOD__));
	}

	public function getSuffix(): ?string
	{
		throw new EmptyImageException(sprintf('Cannot call %s on empty image', __METHOD__));
	}

	/**
	 * @inheritDoc
	 */
	public function getOriginal(): static
	{
		throw new EmptyImageException(sprintf('Cannot call %s on empty image', __METHOD__));
	}

	public function close(?string $reason = null): void
	{
		throw new EmptyImageException(sprintf('Cannot call %s on empty image', __METHOD__));
	}

}
