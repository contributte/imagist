<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\Exceptions\FileException;
use Contributte\Imagist\PathInfo\PathInfoInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface as LeagueFilesystemInterface;

abstract class FilesystemAbstract implements FilesystemInterface
{

	protected LeagueFilesystemInterface $adapter;

	public function __construct(LeagueFilesystemInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @inheritDoc
	 */
	public function exists(PathInfoInterface $path): bool
	{
		return $this->adapter->has($path->toString());
	}

	/**
	 * @inheritDoc
	 */
	public function delete(PathInfoInterface $path): bool
	{
		return $this->withTemporaryConfig(
			'disable_asserts',
			true,
			false,
			fn() => $this->adapter->delete($path->toString())
		);
	}

	/**
	 * @inheritDoc
	 */
	public function listContents(string $path): array
	{
		return $this->adapter->listContents($path);
	}

	/**
	 * @inheritDoc
	 */
	public function put(PathInfoInterface $path, $content, array $config = []): void
	{
		$this->adapter->put($path->toString(), $content, $config);
	}

	/**
	 * @inheritDoc
	 */
	public function putWithMkdir(PathInfoInterface $path, $content, array $config = []): void
	{
		$this->adapter->createDir($path->toString($path::ALL & ~$path::IMAGE));

		$this->put($path, $content, $config);
	}

	/**
	 * @inheritDoc
	 */
	public function read(PathInfoInterface $path): string
	{
		try {
			$content = $this->adapter->read($path->toString());
		} catch (FileNotFoundException $exception) {
			throw new \Contributte\Imagist\Exceptions\FileNotFoundException($exception->getMessage());
		}

		if ($content === false) {
			throw new FileException(sprintf('Cannot read file %s', $path->toString()));
		}

		return $content;
	}

	/**
	 * @inheritDoc
	 */
	public function mimeType(PathInfoInterface $path): ?string
	{
		$mimeType = $this->adapter->getMimetype($path->toString());

		return $mimeType === false ? null : $mimeType;
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @return mixed
	 */
	protected function withTemporaryConfig(string $name, $value, $default, callable $callback) // phpcs:ignore -- phpcs bug
	{
		if (!$this->adapter instanceof Filesystem) {
			return $callback();
		}

		$default = $this->adapter->getConfig()->get($name, $default);

		$this->adapter->getConfig()->set($name, $value);
		$result = $callback();
		$this->adapter->getConfig()->set($name, $default);

		return $result;
	}

}
