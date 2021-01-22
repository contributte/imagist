<?php declare(strict_types = 1);

namespace Contributte\Imagist\Doctrine\Annotation;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Scope
{

	/**
	 * @var string[]
	 */
	private array $scopes;

	/**
	 * @param mixed[] $data
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $data)
	{
		if (!isset($data['value']) || !$data['value']) {
			throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', static::class));
		}

		$value = (array) $data['value'];
		foreach ($value as $scope) {
			if (!is_string($scope)) {
				throw new InvalidArgumentException(
					sprintf('Parameter of annotation "%s" must be a string or an array of strings.', static::class)
				);
			}
		}

		$this->scopes = $value;
	}

	/**
	 * @return string[]
	 */
	public function getScopes(): array
	{
		return $this->scopes;
	}

}
