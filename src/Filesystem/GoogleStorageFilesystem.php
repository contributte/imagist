<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\PathInfo\PathInfoInterface;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

final class GoogleStorageFilesystem extends FilesystemAbstract
{

	public function __construct(string $bucket, string $keyFilePath)
	{
		$client = new StorageClient([
			'keyFilePath' => $keyFilePath,
		]);
		$bucket = $client->bucket($bucket);

		parent::__construct(new GoogleCloudStorageAdapter($bucket));
	}

	/**
	 * {@inheritDoc}
	 */
	public function putWithMkdir(PathInfoInterface $path, string $content, array $config = []): void
	{
		$this->put($path, $content, $config);
	}

}
