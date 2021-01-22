<?php declare(strict_types = 1);

namespace Contributte\Imagist\PathInfo;

final class PathInfo implements PathInfoInterface
{

	private const MAPPING = [
		self::BUCKET => 'bucket',
		self::SCOPE => 'scope',
		self::FILTER => 'filter',
		self::IMAGE => 'name',
	];

	private ?string $scope;

	private ?string $filter;

	private string $name;

	private string $bucket;

	private string $delimiter;

	public function __construct(string $bucket, ?string $scope, ?string $filter, string $name, string $delimiter = '/')
	{
		$this->scope = $scope;
		$this->filter = $filter;
		$this->name = $name;
		$this->bucket = $bucket;
		$this->delimiter = $delimiter;
	}

	public function toString(int $parts = self::ALL): string
	{
		$path = '';
		foreach (self::MAPPING as $bit => $var) {
			if ($parts & $bit && $this->$var) {
				$path .= $this->$var . $this->delimiter;
			}
		}

		if ($path) {
			$path = substr($path, 0, -strlen($this->delimiter));
		}

		return $path;
	}

}
