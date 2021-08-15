# Gumlet + Google Storage

Gumlet is service, that helps to deliver transformed, optimized, resized etc. images to users.
Gumlet reads original images from Google Cloud Storage, Web Folder, Web Proxy, Amazon S3 Bucket, DigitalOcean Spaces Bucket, Wasabi Storage...

Image storage was designed for easy customization, so change will be fast and painless, here we go.

## Google storage - configuration

```neon
extensions:
	image.gumlet: Contributte\Imagist\Bridge\Nette\DI\GumletImageStorageExtension

image.gumlet:
	bucket: bulios
	token: 26982c29aeb19ac8ae94721a096dbe91
```

And that's all. Now we use google storage instead of default filesystem storage.

```neon
services:
    image.filesystem:
        factory: Contributte\Imagist\Filesystem\GoogleStorageFilesystem('bucket', %appDir%/config/gcs.json)
```

## Gumlet - filters

GumletLinkGenerator uses Normalizers (converts filters to array) instead of Filters. So let's create one:

```php
use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use InvalidArgumentException;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Filter\FilterNormalizerProcessorInterface;

final class ImageNormalizer implements FilterNormalizerProcessorInterface
{

    public function avatar(ImageInterface $image): array
	{
	    // resize to 200x200 and crop image
		$gumlet = GumletBuilder::create()
			->resize(200, 200, 'crop');

        // if image matches scope avatar[/*]
		if ($image->getScope()->startsWith('avatar')) {
			$gumlet->crop('faces');
		}

		return $gumlet->build();
	}

	public function supports(ImageFilter $filter, ContextImageAware $context): bool
	{
		$method = $filter->getName();

		return $context->has('gumlet') && !in_array($method, ['supports', 'normalize']) && method_exists($this, $method);
	}

	public function normalize(ImageFilter $filter, ContextImageAware $context): array
	{
		$method = $filter->getName();

		return $this->$method($context->getImage());
	}

}
```

```neon
services:
    - App\Images\Normalizer\ImageNormalizer
```

or with built-in config style configuration:
```neon
extensions:
	image.filters: Contributte\Imagist\Bridge\Nette\DI\ImageStorageConfigFiltersExtension

image.filters:
	sizeM: resize(600)
	avatar:
		- resize(200, 200, crop)
		- crop(faces)
```

We can use image storage as we are used to:

```html
<img n:img="$image|filter:avatar">
```
