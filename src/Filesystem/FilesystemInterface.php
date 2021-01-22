<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use Contributte\Imagist\PathInfo\PathInfoInterface;

interface FilesystemInterface
{

	/**
	 * @param mixed $content
	 * @param mixed[] $config
	 */
	public function putWithMkdir(PathInfoInterface $path, $content, array $config = []): void; // phpcs:ignore -- cs bug

	public function exists(PathInfoInterface $path): bool;

	/**
	 * @return mixed
	 */
	public function delete(PathInfoInterface $path);

	/**
	 * @return mixed[]
	 */
	public function listContents(string $path): array;

	/**
	 * @param mixed $content
	 * @param mixed[] $config
	 */
	public function put(PathInfoInterface $path, $content, array $config = []): void; // phpcs:ignore -- cs bug

	public function read(PathInfoInterface $path): string;

	public function mimeType(PathInfoInterface $path): ?string;

}
