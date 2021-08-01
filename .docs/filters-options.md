# Filters with dynamic options

When we need a lot of variants of filter, we have to create filters individually. With options, we just define one.

Replace filter resolver from default (can't options) to one of:

### Contributte\Imagist\Resolver\FilterResolvers\SimpleFilterResolver
Readable but simple format, options must be list of string, int or bool.

Example of output:
```php
$image->withFilter('resize', [15, 50]); // output: /cache/_resize-15-50/image.jpg
```

Usage:
```yaml
services:
  imagist.resolvers.filter: Contributte\Imagist\Resolver\FilterResolvers\SimpleFilterResolver
```

### Contributte\Imagist\Resolver\FilterResolvers\MD5FilterResolver
Options must array of scalar

Example of output:
```php
$image->withFilter('resize', [15, 50]); // output: /cache/_resize-4a783f58efd342dee8bcbce90617131b/image.jpg
```

Usage:
```yaml
services:
  imagist.resolvers.filter: Contributte\Imagist\Resolver\FilterResolvers\MD5FilterResolver
```

## Options in operation

```php

use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Scope\Scope;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ResizeOperation implements OperationInterface
{

    public function supports(ImageFilter $filter, Scope $scope): bool
    {
        return $filter->getName() === 'resize';
    }

    public function operate(ImageInterface $image, ImageFilter $filter): void
    {
        $image->resize(new Box(...$filter->getOptions()));
    }

}

```

## Usage

PHP:
```php
$image->withFilter('resize', [15, 50]); // output: /cache/_resize-4a783f58efd342dee8bcbce90617131b/image.jpg
```

Latte:
```html
{img $image|filter:resize,15,50}
```
