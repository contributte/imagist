# Filters

Filter is a set of operations that can be applied to an image.
We divide them into static filters (without arguments) and dynamic filters (with arguments).

Note: Operation can be filter too.

## Registration

Let's use `imagine/imagine` as FilterProcessor. We need this setup

```php
use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Filter\FilterProcessor;

$processor = new FilterProcessor(new ImagineResourceFactory(), [
    new ImagineOperationProcessor(),
]);
```

### Nette
```neon
imagist:
	extensions:
		imagine:
			enabled: true
		nette:
			enabled: false
```

## Image Filtering

Using filter in image is pretty easy, just look at it

```php
use Contributte\Imagist\Filter\Operation\ResizeOperation;

$image = $image->withFilter(new ResizeOperation(20, 20)); // resize operation implements FilterInterface
```

Multiple filters are supported too

```php
use Contributte\Imagist\Filter\CompositeFilter;
use Contributte\Imagist\Filter\Operation\CropOperation;

$image = $image->withFilter(new CompositeFilter(
    'composite',
    new ResizeOperation(20, 20),
    new CropOperation(20, 20, 20, 20),
));
```

## Custom filter

Filter has collection of operations. Filter can have arguments, but filters without arguments are more recommended.

```php
use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\ResizeOperation;

final class CustomFilter implements FilterInterface
{

    public function __construct(/* $arguments - if we need */)
    {

    }

    public function getIdentifier(): FilterIdentifier
    {
        return new FilterIdentifier(
          'customName',
          /* $this->arguments - pass all arguments which we need */
        ); // it's important, because this defines name of folder
    }

    /**
     * @return OperationInterface[]
     */
    public function getOperations(): array
    {
        return [
            new ResizeOperation(20, 20),
        ];
    }

}
```

Usage:

```php
$image->withFilter(new CustomFilter(/* $arguments */));
```

## Filters with arguments

By default, filters with arguments aren't allowed. The main problem is name convention of directories.
We must use one of following resolvers instead of default.

### MD5FilterResolver

MD5FilterResolver will convert arguments to json and then to md5 hash

### SimpleFilterResolver

SimpleFilterResolver will convert arguments to an array of strings and then join array with `-` (maximum length is 255 and chars are limited)

### OriginalFilterResolver

OriginalFilterResolver throws away arguments, therefore by default throws exception

Disabling exception throwing

```php
use Contributte\Imagist\Resolver\FilterResolvers\OriginalFilterResolver;

new OriginalFilterResolver(false);
```

## Custom operations

Operations directly manipulate with images. We need create new operation and processor which process this operation.
Here we go:

```php
use Contributte\Imagist\Filter\Operation\OperationInterface;

class RotateOperation implements OperationInterface
{

    public function __construct(public readonly int $degrees) {
    }

}
```

And processor:

```php
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Imagine\Image\ImageInterface;

class CustomOperationProcessor implements OperationProcessorInterface
{

    public function process(object $resource, OperationCollection $collection, ContextInterface $context): void
    {
        if ($resource instanceof ImageInterface) { // supports only imagine/imagine
            return;
        }

        if ($rotate = $collection->get(RotateOperation::class)) { // gets operation if exists, collection deletes this operation for future use
            $resource->rotate($rotate->degrees);
        }
    }

}
```

And usage in filter:

```php
final class CustomFilter implements FilterInterface
{
    // ...

    public function getOperations(): array
    {
        return [
            new RotateOperation(20),
        ];
    }

    // ...
}
```

What if we want to use operation as filter?
```php
$image->withFilter(new RotateOperation(20));
```

Exception is thrown, because filter must be instance of FilterInterface, let's fix it

```php
use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\Operation\OperationAsFilter;

class RotateOperation extends OperationAsFilter
{

    public function __construct(public readonly int $degrees) {
    }

    public function getIdentifier(): FilterIdentifier
    {
        return new FilterIdentifier('rotate', [$this->degrees]);
    }

}
```
