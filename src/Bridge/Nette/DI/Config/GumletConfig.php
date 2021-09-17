<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI\Config;

use Contributte\Imagist\Bridge\Gumlet\GumletLinkGenerator;

final class GumletConfig
{

	public ?string $bucket = null;

	public ?string $token = null;

	public string $domain = GumletLinkGenerator::DEFAULT_DOMAIN;

	public ?string $customDomain = null;

}
