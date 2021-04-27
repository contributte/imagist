# Gumlet + Google Storage

Gumlet is service, that helps to deliver transformed, optimized, resized etc. images to users.
Gumlet reads original images from Google Cloud Storage, Web Folder, Web Proxy, Amazon S3 Bucket, DigitalOcean Spaces Bucket, Wasabi Storage...

Image storage was designed for easy customization, so change will be fast and painless, here we go.

## Google storage - configuration

```yaml
services:
    image.filesystem:
        factory: Contributte\Imagist\Filesystem\GoogleStorageFilesystem('bucketName', %appDir%/path-to/gc-key.json)
```

And that's all. Now we use google storage instead of default filesystem storage.

## Gumlet - configuration

```yaml
services:
    image.linkGenerator:
      factory: Contributte\Imagist\Bridge\Gumlet\GumletLinkGenerator('bucketName', 'secret-key-no-required')
```

GumletLinkGenerator uses Normalizers (converts filters to array) instead of Filters. So let's create one:

```php
use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use InvalidArgumentException;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\FilterNormalizerInterface;

final class ImageNormalizer implements FilterNormalizerInterface
{

    public function avatar(FilterInterface $filter, ImageInterface $image): array
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

	public function normalize(FilterInterface $filter, ImageInterface $image, array $options): array
	{
		$method = $filter->getName();
		if (in_array($method, ['supports', 'normalize']) || !method_exists($this, $method)) {
			throw new InvalidArgumentException(sprintf('Normalizer %s not exists', $method));
		}

		return $this->$method($filter, $image);
	}

}
```

```yaml
services:
    - App\Images\Normalizer\ImageNormalizer
```

We can use image storage as we are used to:

```html
<img n:img="$image|filter:avatar">
```
