<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use InvalidArgumentException;

class GumletBuilder
{

	/** @var mixed[] */
	private array $options = [];

	public static function create(): self
	{
		return new self();
	}

	public function resize(int|string|null $width = null, int|string|null $height = null, ?string $mode = null): self
	{
		if (($width ?? $height) === null) {
			throw new InvalidArgumentException('Height or width must be set');
		}

		if ($width !== null) {
			$this->options['w'] = $width;
		}

		if ($height !== null) {
			$this->options['h'] = $height;
		}

		if ($mode !== null) {
			$this->options['mode'] = $mode;
		}

		return $this;
	}

	public function crop(string $mode): self
	{
		$this->options['crop'] = $mode;

		return $this;
	}

	public function fill(string $fill): self
	{
		$this->options['fill'] = $fill;

		return $this;
	}

	public function mask(string $mask): self
	{
		$this->options['mask'] = $mask;

		return $this;
	}

	public function extract(int|string $left, int|string $top, int|string $width, int|string $height): self
	{
		$this->options['extract'] = implode(',', [$left, $top, $width, $height]);

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function build(): array
	{
		return $this->options;
	}

}
