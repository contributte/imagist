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
  ## shortcuts for class names
  ## defaults are: composite, dynamic
  aliases:
    resize: Contributte\Imagist\Filter\Operation\ResizeOperation
  filters:
    siteS: Contributte\Imagist\Filter\Operation\ResizeOperation(100, 100)
    siteXS: composite(
      resize(100, 100), ## same as Contributte\Imagist\Filter\Operation\ResizeOperation(100, 100)
      Contributte\Imagist\Filter\Operation\CropOperation(20, 20, 20, 20),
    ) ## Multiple operations, first resize, second crop, ...

    ## dynamic filter, supports passing arguments in latte
    ## can be only in root level and with one operation
    resize: dynamic(Contributte\Imagist\Filter\Operation\ResizeOperation)
```

Usage in latte:

```html
<img n:img="$image, filter: siteS">
{* dynamic filter *}
<img n:img="$image, filter: [resize, 200, 200]">
```
