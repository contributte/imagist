# Nette filters via neon config

For basic usage you can use simpler way than defining filters in classes. We use only neon config.


## Registration

```yaml
extensions:
  imagist.filters: Contributte\Imagist\Bridge\Nette\DI\ImageStorageConfigFiltersExtension
```

That's all. We can use [nette/utils](nette-image-filters.md) or [imagine/imagine](imagine.md) for image operations.

Built-in operations are

**nette/utils**

```php
/**
 * @param $flag one of fit, fill, exact, shrink_only, stretch
 */
resize(int|string|null $width, int|string|null $height = null, string $flag = 'fit')

crop(int|string $left, int|string $top, int|string $width, int|string $height);

/**
 * @param $mode one of vertical, horizontal, both
 */
flip(string $mode);

sharpen();
```

**imagine/imagine**

```php
resize(int $width, int $height);
```

## Usage

```yaml
imagist.filters:
  siteS: resize(200, 200, fill) ## Single operation
  siteXS: ## Multiple operations, first resize, second flip, ...
    - resize(100, 100, fill)
    - flip(vertical)
    - crop(20, 20, 20, 20)
    - sharpen()
```

Traditional usage in latte:

```html
<img n:img="$image|filter:siteS">
```

## Custom operation in config

**nette/utils**
```php
use Contributte\Imagist\Bridge\Nette\Filter\Config\NetteConfigOperationInterface;

final class SharpenConfigOperation implements NetteConfigOperationInterface
{

	public function getName(): string
	{
		return 'sharpen2';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
		$image->sharpen();
	}

}
```

**imagine/imagine**

```php

use Contributte\Imagist\Bridge\Imagine\Config\ImagineConfigOperationInterface;

final class BlurConfigOperation implements ImagineConfigOperationInterface
{

	public function getName(): string
	{
		return 'blur';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function operate(ImageInterface $image, FilterInterface $filter, NetteImageOptions $options, array $arguments): void
	{
	    [$amount] = $arguments;

		$image->effects()->blur($amount);
	}

}
```

Register as service

## Tracy

![tracy](https://raw.githubusercontent.com/contributte/imagist/master/.docs/img/tracy-filters.png)
