<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use InvalidArgumentException;

class GumletBuilder
{

	/** @var mixed[] */
	private array $options = [];

	/**
	 * @return self
	 */
	public static function create()
	{
		return new self();
	}

	/**
	 * @param int|string|null $width
	 * @param int|string|null $height
	 * @return self
	 */
	public function resize($width = null, $height = null, ?string $mode = null)
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

	/**
	 * @return self
	 */
	public function crop(string $mode)
	{
		$this->options['crop'] = $mode;

		return $this;
	}

	/**
	 * @return self
	 */
	public function mask(string $mask)
	{
		$this->options['mask'] = $mask;

		return $this;
	}

	/**
	 * @param int|string $left
	 * @param int|string $top
	 * @param int|string $width
	 * @param int|string $height
	 */
	public function extract($left, $top, $width, $height): self
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
