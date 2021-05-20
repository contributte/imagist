# Imagine filters

- [Registration](#registration)
  - [Nette registration](#nette)
- [Operations](#operations)

## Registration

### Nette

Just install imagine package `composer require imagine/imagine`, nette extension automatically register
it and will use

## Operations

For image manipulating imagine extension uses operations. Let's go define one and register it as service.

```php

use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Imagine\Image\ImageInterface;

class SharpenOperation implements OperationInterface
{

    public function supports(FilterInterface $filter, Scope $scope): bool
    {
        return $filter->getName() === 'sharpen';
    }

    public function operate(ImageInterface $image, FilterInterface $filter): void
    {
        $image->effects()->sharpen();
    }

}

```

```yaml
services:
  - SharpenOperation
```

Now we can use filter 'sharpen':

```html
<img n:img="$image|filter:sharpen">
```
