<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Macro;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\MacroTokens;
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
		$word = $node->tokenizer->fetchWord();
		$filters = $this->extractFilter($node, $writer);
		if ($filters) {
			$filter = $writer->formatArray(new MacroTokens($filters));
		} else {
			$filter = '[]';
		}

		$options = $writer->formatArray(new MacroTokens($node->tokenizer->nextAll()));

		return $writer->write(
			'%node.line ' .
			'echo $this->global->images->link(' .
			(($word[0] ?? null) === '$' ? '%raw' : '%word') . ', ' .
			'%raw,' .
			'%raw' .
			');',
			$word,
			$options,
			$filter
		);
	}

	public function attrMacro(MacroNode $node, PhpWriter $writer): string
	{
		$htmlNode = $node->htmlNode;
		if (!$htmlNode) {
			throw new LogicException('Macro n:img must be in a or img attribute');
		}

		$attr = $htmlNode->name === 'img' ? '" src="' : '" href="';
		if (!$node->modifiers && ($pos = strpos($node->args, '|')) !== false) {
			$node->modifiers = substr($node->args, $pos);
			$node->setArgs(substr($node->args, 0, $pos));
		}

		return $writer->write(
			'echo %raw . "\""; %raw echo "\""',
			$attr,
			$this->beginMacro($node, $writer)
		);
	}

	private function extractFilter(MacroNode $node, PhpWriter $writer): string
	{
		$filter = null;
		// backward compatability
		if (strncmp($node->modifiers, '|filter', strlen('|filter')) === 0) {
			$node->modifiers = (string) preg_replace_callback('#\|\s*filter\s*:\s*([^\|]+)#', function (array $matches) use (&$filter): string {
				if ($filter !== null) {
					throw new CompileException('Cannot use two or more filters.');
				}

				$filter = $matches[1];

				return '';
			}, $node->modifiers);
		} else {
			$modifiers = array_map(function (string $modifier) use ($writer): string {
				if (strpos($modifier, ':') === false) {
					return $modifier;
				}

					$options = $writer->formatArray(new MacroTokens(trim(substr($modifier, strpos($modifier, ':') + 1))));

					return substr($modifier, 0, strpos($modifier, ':') + 1) . $options;
			}, explode('|', ltrim($node->modifiers, '|')));
			$filter = implode(',', $modifiers);

			$node->modifiers = '';
		}

		return (string) $filter;
	}

}
