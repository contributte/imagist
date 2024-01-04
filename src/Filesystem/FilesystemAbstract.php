<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\Exceptions\FileException;
use Contributte\Imagist\Exceptions\FileNotFoundException;
use Contributte\Imagist\PathInfo\PathInfoInterface;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;

abstract class FilesystemAbstract implements FilesystemInterface
{

	protected FilesystemAdapter $adapter;

	public function __construct(FilesystemAdapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists(PathInfoInterface $path): bool
	{
		return $this->adapter->fileExists($path->toString());
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(PathInfoInterface $path): bool
	{
		try {
			$this->adapter->delete($path->toString());
		} catch (UnableToDeleteFile $e) {
			throw new FileException(sprintf('Cannot delete file %s', $path->toString()), 0, $e);
		}

		return true;
	}

	/**
	 * @return array<StorageAttributes>
	 */
	public function listContents(string $path): iterable
	{
		return $this->adapter->listContents($path, false);
	}

	/**
	 * {@inheritDoc}
	 */
	public function put(PathInfoInterface $path, string $content, array $config = []): void
	{
		$this->adapter->write($path->toString(), $content, new Config($config));
	}

	/**
	 * {@inheritDoc}
	 */
	public function putWithMkdir(PathInfoInterface $path, string $content, array $config = []): void
	{
		$this->adapter->createDirectory($path->toString($path::BUCKET | $path::SCOPE | $path::FILTER), new Config());

		$this->put($path, $content, $config);
	}

	/**
	 * {@inheritDoc}
	 */
	public function read(PathInfoInterface $path): string
	{
		try {
			$content = $this->adapter->read($path->toString());
		} catch (UnableToReadFile $e) {
			throw new FileNotFoundException(sprintf('Cannot read file %s', $path->toString()), 0, $e);
		} catch (FilesystemException $e) {
			throw new FileNotFoundException($e->getMessage(), 0, $e);
		}

		return $content;
	}

	/**
	 * {@inheritDoc}
	 */
	public function mimeType(PathInfoInterface $path): ?string
	{
		try {
			$mimeType = $this->adapter->mimeType($path->toString());
		} catch (UnableToRetrieveMetadata $e) {
			throw new FileException(sprintf('Cannot get mime type of file %s', $path->toString()), 0, $e);
		} catch (FilesystemException $e) {
			throw new FileNotFoundException($e->getMessage(), 0, $e);
		}

		return $mimeType->mimeType();
	}

}
