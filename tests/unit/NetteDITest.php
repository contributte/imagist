<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Codeception\Test\Unit;
use Contributte\Imagist\Bridge\Nette\DI\ImageStorageExtension;
use Contributte\Imagist\Bridge\Nette\LinkGenerator;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Spatie\TemporaryDirectory\TemporaryDirectory;

final class NetteDITest extends Unit
{

	private TemporaryDirectory $tempDir;

	private Container $container;

	protected function _before(): void
	{
		$this->tempDir = new TemporaryDirectory(__DIR__ . '/_tmp');
		$this->tempDir->delete();

		$loader = new ContainerLoader($this->tempDir->path('di'));
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addConfig([
				'parameters' => [
					'wwwDir' => $this->tempDir->path('www'),
				],
			]);
			$compiler->addExtension('http', new HttpExtension());
			$compiler->addExtension('imageStorage', new ImageStorageExtension());
		});

		$this->container = new $class();
	}

	protected function _after(): void
	{
		$this->tempDir->delete();
	}

	public function testCompilerClasses(): void
	{
		$this->assertInstanceOf(LinkGenerator::class, $this->container->getByType(LinkGeneratorInterface::class));
		$this->assertInstanceOf(FilterProcessorInterface::class, $this->container->getByType(FilterProcessorInterface::class));
	}

}
