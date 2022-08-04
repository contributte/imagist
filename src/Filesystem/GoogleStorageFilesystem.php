<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\PathInfo\PathInfoInterface;
use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem;
use LogicException;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

final class GoogleStorageFilesystem extends FilesystemAbstract
{

	public function __construct(string $bucket, string $keyFilePath)
	{
		if (!class_exists(GoogleStorageAdapter::class)) {
			throw new LogicException(
				sprintf(
					'%s not found, install it by composer install superbalist/flysystem-google-storage',
					self::class
				)
			);
		}

		$client = new StorageClient([
			'keyFilePath' => $keyFilePath,
		]);
		$bucket = $client->bucket($bucket);

		parent::__construct(new Filesystem(
			new GoogleStorageAdapter($client, $bucket)
		));
	}

	/**
	 * @inheritDoc
	 */
	public function putWithMkdir(PathInfoInterface $path, string $content, array $config = []): void
	{
		$this->put($path, $content, $config);
	}

	public function delete(PathInfoInterface $path): bool
	{
		try {
			return parent::delete($path);
		} catch (NotFoundException $exception) {
			return false;
		}
	}

}
