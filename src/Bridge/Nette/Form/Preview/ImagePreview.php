<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\Filter;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use LogicException;
use Nette\Utils\Html;

class ImagePreview implements ImagePreviewInterface
{

	private LinkGeneratorInterface $linkGenerator;

	private ?FilterInterface $filter = null;

	private ?PersistentImageInterface $placeholder = null;

	private ?string $placeholderLink = null;

	public function __construct(LinkGeneratorInterface $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @return static
	 */
	public function setFilterObject(?FilterInterface $filter)
	{
		$this->filter = $filter;

		return $this;
	}

	/**
	 * @param mixed[] $options
	 * @return static
	 */
	public function setFilter(string $name, array $options = [])
	{
		$this->filter = new Filter($name, $options);

		return $this;
	}

	/**
	 * @return static
	 */
	public function setPlaceholderLink(?string $link)
	{
		if ($this->placeholder) {
			throw new LogicException('Cannot set placeholder with placeholder link');
		}

		$this->placeholderLink = $link;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setPlaceholder(?PersistentImageInterface $placeholder)
	{
		if ($this->placeholderLink) {
			throw new LogicException('Cannot set placeholder with placeholder link');
		}

		$this->placeholder = $placeholder;

		return $this;
	}

	public function hasImage(ImageUploadControl $input): bool
	{
		return $input->getValue()->getDefault() || $this->placeholderLink;
	}

	public function getHtml(ImageUploadControl $input): ?Html
	{
		$value = $input->getValue();
		$placeholder = $this->placeholderLink;

		if ($this->placeholder) {
			$placeholder = $this->linkGenerator->link($this->placeholder);
		}

		$wrapper = Html::el('div', [
			'class' => ['image-upload-preview-wrapper'],
		]);

		if ($default = $value->getDefault()) {
			$wrapper->create('img', [
				'src' => $this->linkGenerator->link(
					$default->withFilterObject($this->filter)
				),
				'class' => ['image-upload-preview'],
				'data-placeholder' => $placeholder,
			]);
		} elseif ($placeholder) {
			$wrapper->create('img', [
				'src' => $placeholder,
				'class' => ['image-upload-preview'],
				'data-placeholder' => $placeholder,
			]);
		} else {
			$wrapper->appendAttribute('class', 'empty');
		}

		return $wrapper;
	}

}
