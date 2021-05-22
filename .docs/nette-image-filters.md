# Nette image filters

- [Registration](#registration)
- [Operations](#operations)

## Registration

```yaml
imagist:
  extensions:
    nette:
      filters:
        enabled: true
```

## Operations

For image manipulating nette image extension uses operations. Let's go define one and register it as service.

```php

use Contributte\Imagist\Bridge\NetteImage\NetteImageOptions;
use Contributte\Imagist\Bridge\NetteImage\NetteOperationInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

class SharpenOperation implements NetteOperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool
	{
		return $filter->getName() === 'sharpen';
	}

	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options): void
	{
		$image->sharpen();
	}

}

```

```yaml
services:
  - SharpenOperation
```
