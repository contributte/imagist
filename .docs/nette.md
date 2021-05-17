# Nette

- [DI](#di)
- [Forms](#forms)
- [Latte](#latte)
- [Tracy](#tracy)

## DI
Register extension

```yaml
extensions:
  imagist: Contributte\Imagist\Bridge\Nette\DI\ImageStorageExtension
```

## Latte

Original image:
```html
{varType Contributte\Imagist\Entity\PersistentImageInterface $image}

<a n:href="$image">
  <img n:img="$image">
</a>

{img $image}
```

Image with filter
```html
{varType Contributte\Imagist\Entity\PersistentImageInterface $image}

<img n:img="$image|filter:miniAvatar">
```

Image with options
```html
{varType Contributte\Imagist\Entity\PersistentImageInterface $image}

<img n:img="$image, scope => 'avatar'">
```

same in php

```php
$linkGenerator->link($image, ['scope' => 'avatar']);
```

## Forms

Add new method to nette forms:

```php

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Scope\Scope;

class Form extends \Nette\Application\UI\Form
{

    public function addImageUpload(string $name, ?string $label = null, ?string $scope = null): ImageUploadControl
	{
		$control = $this[$name] = new ImageUploadControl($label);
		if ($scope) {
			$control->setScope(new Scope($scope));
		}

		return $control;
	}

}

```

Usage:
```php
$form = new Form();

$form->addImageUpload('image');
```

Default value:

```php
use Contributte\Imagist\Entity\PersistentImage;

$form = new Form();

$form->addImageUpload('image')
    ->setDefaultValue(new PersistentImage('image.png'));

// or

$form->setDefaults([
    'image' => new PersistentImage('image.png'),
]);
```

Upload with preview:

```php
use Contributte\Imagist\Entity\PersistentImage;

assert($imagePreviewFactory instanceof Contributte\Imagist\Bridge\Nette\Form\Preview\ImagePreviewFactoryInterface); // inject

$form = new Form();

$form->addImageUpload('image')
    ->setPreview($imagePreviewFactory->create())
    ->setDefaultValue(new PersistentImage('image.png'));
```

Add checkbox for removing image:

```php
use Contributte\Imagist\Bridge\Nette\Form\Remove\ImageRemove;
use Contributte\Imagist\Entity\PersistentImage;

$form = new Form();

$form->addImageUpload('image')
    ->setRemove(new ImageRemove('Check to delete image'))
    ->setDefaultValue(new PersistentImage('image.png'));
```

## Tracy

Package provides tracy debug bar with bluescreen extension

![tracy](https://raw.githubusercontent.com/contributte/imagist/master/.docs/img/tracy.png)
