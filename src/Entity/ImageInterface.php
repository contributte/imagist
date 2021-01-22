<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

interface ImageInterface
{

	/**
	 * Combination of scope and name
	 *
	 * @example scope/image.jpg
	 */
	public function getId(): string;

	/**
	 * Name of image
	 *
	 * @example image.jpg
	 */
	public function getName(): string;

	/**
	 * Suffix of image
	 *
	 * @example jpg
	 */
	public function getSuffix(): ?string;

	/**
	 * Scope of image
	 */
	public function getScope(): Scope;

	/**
	 * Filter object of image
	 */
	public function getFilter(): ?FilterInterface;

	/**
	 * Checks if image has filter
	 */
	public function hasFilter(): bool;

	/**
	 * Checks if image is closed
	 */
	public function isClosed(): bool;

	/**
	 * Checks if image is empty
	 */
	public function isEmpty(): bool;

	public function isPromise(): bool;

	public function equalTo(ImageInterface $image): bool;

	/**
	 * Returns image without filter
	 *
	 * @return static
	 */
	public function getOriginal();

	/**
	 * @return static
	 */
	public function withName(string $name);

	/**
	 * @return static
	 */
	public function withScope(Scope $scope);

	/**
	 * @param mixed[] $options
	 * @return static
	 */
	public function withFilter(string $name, array $options = []);

	/**
	 * @return static
	 */
	public function withFilterObject(?FilterInterface $filter);

}
