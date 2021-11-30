# Nette filters via neon config

For basic usage you can use simpler way than defining filters in classes. We use only neon config.


## Registration

```yaml
extensions:
  imagist.filters: Contributte\Imagist\Bridge\Nette\DI\ImageStorageConfigFiltersExtension
```

That's all. We use [nette/utils](nette-processor.md) or [imagine/imagine](imagine.md) for image operations.

## Usage

```yaml
imagist.filters:
  siteS: Contributte\Imagist\Filter\Operation\ResizeOperation(100, 100)
  siteXS: Contributte\Imagist\Filter\CompositeFilter(
    'siteXS', # name of filter,
    Contributte\Imagist\Filter\Operation\ResizeOperation(100, 100),
    Contributte\Imagist\Filter\Operation\CropOperation(20, 20, 20, 20),
  ) ## Multiple operations, first resize, second crop, ...
```

Usage in latte:

```html
<img n:img="$image|filter:siteS">
```
