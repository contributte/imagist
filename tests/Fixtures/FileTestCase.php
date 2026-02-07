<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Contributte\Tester\Environment;
use Nette\Utils\FileSystem;
use Tester\Assert;

class FileTestCase
{

	public string $imageJpg;

	private string $path;

	private string $origImageJpg = __DIR__ . '/image.jpg';

	public function __construct()
	{
		$this->path = Environment::getTestDir() . '/';
		$this->imageJpg = Environment::getTestDir() . '/tmp-image.jpg';

		FileSystem::createDir($this->path);
		FileSystem::copy($this->origImageJpg, $this->imageJpg);
	}

	public function cleanup(): void
	{
		FileSystem::delete($this->path);

		if (file_exists($this->imageJpg)) {
			FileSystem::delete($this->imageJpg);
		}
	}

	public function assertTempFileExists(string $relativePath): void
	{
		Assert::true(file_exists($this->path . ltrim($relativePath, '/')));
	}

	public function assertTempFileNotExists(string $relativePath): void
	{
		Assert::false(file_exists($this->path . ltrim($relativePath, '/')));
	}

	public function getAbsolutePath(?string $relative = null): string
	{
		if ($relative === null) {
			return $this->path;
		}

		return $this->path . ltrim($relative, '/');
	}

}
