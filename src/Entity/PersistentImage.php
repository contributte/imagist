<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Scope\Scope;

class PersistentImage extends Image implements PersistentImageInterface
{

	public function __construct(string $id)
	{
		parent::__construct(...$this->parseId($id));
	}

	/**
	 * @return array{string, Scope}
	 */
	protected function parseId(string $id): array
	{
		$explode = explode('/', $id);
		$last = array_key_last($explode);
		$name = $explode[$last];
		unset($explode[$last]);

		return [$name, new Scope(...$explode)];
	}

	public function close(?string $reason = null): void
	{
		$this->setClosed($reason);
	}

}
