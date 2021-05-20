<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy;

use Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen\BlueScreenBacktraceInterface;
use Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen\ExceptionImageProviderInterface;
use Throwable;
use Tracy\BlueScreen;

final class ImagistBlueScreen
{

	public static function install(BlueScreen $blueScreen): void
	{
		$blueScreen->addPanel(function (?Throwable $exception) use ($blueScreen): ?array {
			if (!$exception instanceof BlueScreenBacktraceInterface || !$exception->getBackTrace()) {
				return null;
			}

			// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
			$expanded = null;
			$stack = $exception->getBackTrace();
			$dump = $blueScreen->getDumper();
			foreach ($stack as $key => $trace) {
				if (!isset($trace['file']) || !self::isCollapsed($blueScreen, $trace['file'])) {
					$expanded = $key;

					break;
				}
			}

			// phpcs:enable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable

			ob_start();
			require __DIR__ . '/assets/stack.phtml';
			$panel = ob_get_clean();

			return [
				'tab' => 'Close backtrace',
				'panel' => $panel,
			];
		});

		$blueScreen->addPanel(function (?Throwable $exception) use ($blueScreen): ?array {
			if (!$exception instanceof ExceptionImageProviderInterface) {
				return null;
			}

			return [
				'tab' => 'Source image',
				'panel' => ($blueScreen->getDumper())($exception->getImage()),
			];
		});
	}

	private static function isCollapsed(BlueScreen $blueScreen, string $file): bool
	{
		$collapsePaths = $blueScreen->collapsePaths;
		$collapsePaths[] = dirname(__DIR__, 4);

		$file = strtr($file, '\\', '/') . '/';
		foreach ($collapsePaths as $path) {
			$path = strtr($path, '\\', '/') . '/';
			if (strncmp($file, $path, strlen($path)) === 0) {
				return true;
			}
		}

		return false;
	}

}
