<?php declare(strict_types = 1);

namespace Contributte\Imagist\Doctrine\Annotation;

use Contributte\Imagist\Doctrine\Annotation\Scope as ScopeAnnotation;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Scope\Scope;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionProperty;

class AnnotationScopeProvider
{

	private Reader $reader;

	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @phpstan-param class-string $class
	 */
	public function process(ImageInterface $image, string $class, string $property): ImageInterface
	{
		$class = new ReflectionClass($class);
		$property = $class->getProperty($property);

		return $this->processFromReflection($image, $property);
	}

	public function processFromReflection(ImageInterface $image, ReflectionProperty $property): ImageInterface
	{
		$scopes = $this->getScopes($property);
		if ($scopes === null) {
			return $image;
		}

		$scope = new Scope(...$scopes);

		return $image->withScope($scope);
	}

	/**
	 * @return string[]|null
	 */
	protected function getScopes(ReflectionProperty $property): ?array
	{
		/** @var ScopeAnnotation|null $annotation */
		$annotation = $this->reader->getPropertyAnnotation($property, ScopeAnnotation::class);
		if (!$annotation) {
			return null;
		}

		return $annotation->getScopes();
	}

}
