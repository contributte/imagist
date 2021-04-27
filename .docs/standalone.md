## Standalone

Minimal configuration

```php
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\VoidFilterProcessor;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\PersisterRegistry;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Remover\RemoverRegistry;
use Contributte\Imagist\Resolver\DefaultImageResolvers\NullDefaultImageResolver;
use Contributte\Imagist\Resolver\FileNameResolvers\OriginalFileNameResolver;
use Contributte\Imagist\Storage\ImageStorage;

// filter processor
$processor = new VoidFilterProcessor();

// file and path
$fileFactory = new FileFactory(
	$filesystem = new LocalFilesystem('/path/to/root/dir'),
	$pathInfoFactory = new PathInfoFactory()
);

// default images
$defaultImageResolver = new NullDefaultImageResolver();

// persisters
$persisterRegistry = new PersisterRegistry();
$persisterRegistry->add(new EmptyImagePersister());
$persisterRegistry->add(new PersistentImagePersister($fileFactory, $processor));
$persisterRegistry->add(new StorableImagePersister($fileFactory, $processor, new OriginalFileNameResolver()));

// removers
$removerRegistry = new RemoverRegistry();
$removerRegistry->add(new EmptyImageRemover());
$removerRegistry->add(new PersistentImageRemover($fileFactory, $pathInfoFactory, $filesystem));

// storage
$storage = new ImageStorage($persisterRegistry, $removerRegistry);

// link generator
$linkGenerator = new LinkGenerator($storage, $fileFactory, $defaultImageResolver);
```
