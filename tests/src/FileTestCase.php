<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing;

use Codeception\Test\Unit;
use Symfony\Component\Filesystem\Filesystem;

abstract class FileTestCase extends Unit
{

	private Filesystem $filesystem;

	private string $path;

	private string $origImageJpg = __DIR__ . '/../_data/image.jpg';

	protected string $imageJpg = __DIR__ . '/../_data/tmp-image.jpg';

	protected function _before(): void
	{
		$this->filesystem = new Filesystem();

		$this->filesystem->mkdir($this->path = __DIR__ . '/tmp/');
		$this->filesystem->copy($this->origImageJpg, $this->imageJpg);
	}

	protected function _after(): void
	{
		$this->filesystem->remove([__DIR__ . '/tmp', $this->imageJpg]);
	}

	protected function assertTempFileExists(string $relativePath): void
	{
		$this->assertFileExists($this->path . ltrim($relativePath, '/'));
	}

	protected function assertTempFileNotExists(string $relativePath): void
	{
		$this->assertFileNotExists($this->path . ltrim($relativePath, '/'));
	}

	protected function getAbsolutePath(?string $relative = null): string
	{
		if (!$relative) {
			return $this->path;
		}

		return $this->path . ltrim($relative, '/');
	}

}
