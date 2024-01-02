<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use LogicException;
use Nette\Utils\Html;

class ImagePreview implements ImagePreviewInterface
{

	private Html $imagePrototype;

	private Html $wrapperPrototype;

	private LinkGeneratorInterface $linkGenerator;

	private ?FilterInterface $filter = null;

	private ?PersistentImageInterface $placeholder = null;

	private ?string $placeholderLink = null;

	public function __construct(LinkGeneratorInterface $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
		$this->imagePrototype = Html::el('img', ['class' => 'image-upload-preview']);
		$this->wrapperPrototype = Html::el('div', ['class' => 'image-upload-preview-wrapper']);
	}

	public function getImagePart(): Html
	{
		return $this->imagePrototype;
	}

	public function getWrapperPart(): Html
	{
		return $this->wrapperPrototype;
	}

	/**
	 * @return static
	 */
	public function setFilter(FilterInterface $filter): static
	{
		$this->filter = $filter;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setPlaceholderLink(?string $link): static
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
	public function setPlaceholder(?PersistentImageInterface $placeholder): static
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

		$wrapper = clone $this->wrapperPrototype;

		if ($default = $value->getDefault()) {
			$img = clone $this->imagePrototype;

			$img->setAttribute('src', $this->linkGenerator->link($default->withFilter($this->filter)));
			$img->setAttribute('data-placeholder', $placeholder);

			$wrapper->insert(0, $img);
		} elseif ($placeholder) {
			$img = clone $this->imagePrototype;

			$img->setAttribute('src', $placeholder);
			$img->setAttribute('data-placeholder', $placeholder);

			$wrapper->insert(0, $img);
		} else {
			$wrapper->appendAttribute('class', 'empty');
		}

		return $wrapper;
	}

}
