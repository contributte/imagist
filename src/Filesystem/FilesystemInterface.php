<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\PathInfo\PathInfoInterface;

interface FilesystemInterface
{

	/**
	 * @param mixed[] $config
	 */
	public function putWithMkdir(PathInfoInterface $path, string $content, array $config = []): void; // phpcs:ignore -- cs bug

	public function exists(PathInfoInterface $path): bool;

	public function delete(PathInfoInterface $path): mixed;

	/**
	 * @return mixed[]
	 */
	public function listContents(string $path): iterable;

	/**
	 * @param mixed[] $config
	 */
	public function put(PathInfoInterface $path, string $content, array $config = []): void; // phpcs:ignore -- cs bug

	public function read(PathInfoInterface $path): string;

	public function mimeType(PathInfoInterface $path): ?string;

}
