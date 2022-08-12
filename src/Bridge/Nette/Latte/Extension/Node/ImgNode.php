<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Latte\Extension\Node;

use Latte\Compiler\Nodes\Html\ElementNode;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

final class ImgNode extends StatementNode
{

	private ?ElementNode $element;

	private ExpressionNode $image;

	private ArrayNode $arguments;

	public static function create(Tag $tag): self
	{
		$tag->expectArguments();

		$node = new self();
		$node->image = $tag->parser->parseUnquotedStringOrExpression();

		$tag->parser->stream->tryConsume(',');

		$node->arguments = $tag->parser->parseArguments();
		$node->element = $tag->isNAttribute() ? $tag->htmlElement : null;

		return $node;
	}

	public function print(PrintContext $context): string
	{
		$code = $context->format('$this->global->imagist->link(%node, %node?)', $this->image, $this->arguments);

		if ($this->element) {
			$attribute = match ($this->element->name) {
				'img', 'script' => 'src',
				default => 'href',
			}; // @phpcs:ignore

			$code = sprintf('echo \' %s="\' . %s . \'"\';', $attribute, $code);
		} else {
			$code = sprintf('echo %s;', $code);
		}

		return $code;
	}

}
