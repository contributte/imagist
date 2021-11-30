<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Macro;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use LogicException;

final class ImageMacro extends MacroSet
{

	public static function install(Compiler $compiler): void
	{
		$me = new self($compiler);

		$me->addMacro('img', [$me, 'beginMacro'], null, [$me, 'attrMacro']);
	}

	public function beginMacro(MacroNode $node, PhpWriter $writer): string
	{
		$name = $node->tokenizer->fetchWord();
		if (!$name) {
			throw new CompileException('Missing image name in {img}');
		}

		return $writer->write(
			'%node.line ' .
			'echo $this->global->images->link(' . ($name[0] === '$' ? '%raw' : '%word') . ', %node.array?);',
			$name
		);
	}

	public function attrMacro(MacroNode $node, PhpWriter $writer): string
	{
		$htmlNode = $node->htmlNode;
		if (!$htmlNode) {
			throw new LogicException('Macro n:img must be in a or img attribute');
		}

		$attr = $htmlNode->name === 'img' ? '" src="' : '" href="';

		return $writer->write(
			'echo %raw . "\""; %raw echo "\""',
			$attr,
			$this->beginMacro($node, $writer)
		);
	}

}
