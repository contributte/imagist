<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Bridge\Nette\DI\ImageStorageExtension;
use Contributte\Imagist\Bridge\Nette\LinkGenerator;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$tempDir = Environment::getTestDir();

	$loader = new ContainerLoader($tempDir . '/di');
	$class = $loader->load(static function (Compiler $compiler) use ($tempDir): void {
		$compiler->addConfig([
			'parameters' => [
				'wwwDir' => $tempDir . '/www',
			],
		]);
		$compiler->addExtension('http', new HttpExtension());
		$compiler->addExtension('imageStorage', new ImageStorageExtension());
	});

	$container = new $class();

	Assert::type(LinkGenerator::class, $container->getByType(LinkGeneratorInterface::class));
	Assert::type(FilterProcessorInterface::class, $container->getByType(FilterProcessorInterface::class));
});
