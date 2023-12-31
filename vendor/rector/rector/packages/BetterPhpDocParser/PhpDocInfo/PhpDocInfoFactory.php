<?php

declare (strict_types=1);
namespace Rector\BetterPhpDocParser\PhpDocInfo;

use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use Rector\BetterPhpDocParser\Annotation\AnnotationNaming;
use Rector\BetterPhpDocParser\PhpDocNodeFinder\PhpDocNodeByTypeFinder;
use Rector\BetterPhpDocParser\PhpDocNodeMapper;
use Rector\BetterPhpDocParser\PhpDocParser\BetterPhpDocParser;
use Rector\BetterPhpDocParser\ValueObject\Parser\BetterTokenIterator;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\BetterPhpDocParser\ValueObject\StartAndEnd;
use Rector\ChangesReporting\Collector\RectorChangeCollector;
use Rector\Core\Configuration\CurrentNodeProvider;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\StaticTypeMapper\StaticTypeMapper;
final class PhpDocInfoFactory
{
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocNodeMapper
     */
    private $phpDocNodeMapper;
    /**
     * @readonly
     * @var \Rector\Core\Configuration\CurrentNodeProvider
     */
    private $currentNodeProvider;
    /**
     * @readonly
     * @var \PHPStan\PhpDocParser\Lexer\Lexer
     */
    private $lexer;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocParser\BetterPhpDocParser
     */
    private $betterPhpDocParser;
    /**
     * @readonly
     * @var \Rector\StaticTypeMapper\StaticTypeMapper
     */
    private $staticTypeMapper;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\Annotation\AnnotationNaming
     */
    private $annotationNaming;
    /**
     * @readonly
     * @var \Rector\ChangesReporting\Collector\RectorChangeCollector
     */
    private $rectorChangeCollector;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocNodeFinder\PhpDocNodeByTypeFinder
     */
    private $phpDocNodeByTypeFinder;
    /**
     * @var array<string, PhpDocInfo>
     */
    private $phpDocInfosByObjectHash = [];
    public function __construct(PhpDocNodeMapper $phpDocNodeMapper, CurrentNodeProvider $currentNodeProvider, Lexer $lexer, BetterPhpDocParser $betterPhpDocParser, StaticTypeMapper $staticTypeMapper, AnnotationNaming $annotationNaming, RectorChangeCollector $rectorChangeCollector, PhpDocNodeByTypeFinder $phpDocNodeByTypeFinder)
    {
        $this->phpDocNodeMapper = $phpDocNodeMapper;
        $this->currentNodeProvider = $currentNodeProvider;
        $this->lexer = $lexer;
        $this->betterPhpDocParser = $betterPhpDocParser;
        $this->staticTypeMapper = $staticTypeMapper;
        $this->annotationNaming = $annotationNaming;
        $this->rectorChangeCollector = $rectorChangeCollector;
        $this->phpDocNodeByTypeFinder = $phpDocNodeByTypeFinder;
    }
    public function createFromNodeOrEmpty(Node $node) : \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo
    {
        // already added
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        if ($phpDocInfo instanceof \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo) {
            return $phpDocInfo;
        }
        $phpDocInfo = $this->createFromNode($node);
        if ($phpDocInfo instanceof \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo) {
            return $phpDocInfo;
        }
        return $this->createEmpty($node);
    }
    public function createFromNode(Node $node) : ?\Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo
    {
        $objectHash = \spl_object_hash($node);
        if (isset($this->phpDocInfosByObjectHash[$objectHash])) {
            return $this->phpDocInfosByObjectHash[$objectHash];
        }
        /** @see \Rector\BetterPhpDocParser\PhpDocParser\DoctrineAnnotationDecorator::decorate() */
        $this->currentNodeProvider->setNode($node);
        $docComment = $node->getDocComment();
        if (!$docComment instanceof Doc) {
            if ($node->getComments() === []) {
                return null;
            }
            // create empty node
            $tokenIterator = new BetterTokenIterator([]);
            $phpDocNode = new PhpDocNode([]);
        } else {
            $comments = $node->getComments();
            $docs = \array_filter($comments, static function (Comment $comment) : bool {
                return $comment instanceof Doc;
            });
            if (\count($docs) > 1) {
                $this->storePreviousDocs($node, $comments, $docComment);
            }
            $text = $docComment->getText();
            $tokens = $this->lexer->tokenize($text);
            $tokenIterator = new BetterTokenIterator($tokens);
            $phpDocNode = $this->betterPhpDocParser->parse($tokenIterator);
            $this->setPositionOfLastToken($phpDocNode);
        }
        $phpDocInfo = $this->createFromPhpDocNode($phpDocNode, $tokenIterator, $node);
        $this->phpDocInfosByObjectHash[$objectHash] = $phpDocInfo;
        return $phpDocInfo;
    }
    /**
     * @api
     */
    public function createEmpty(Node $node) : \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo
    {
        /** @see \Rector\BetterPhpDocParser\PhpDocParser\DoctrineAnnotationDecorator::decorate() */
        $this->currentNodeProvider->setNode($node);
        $phpDocNode = new PhpDocNode([]);
        $phpDocInfo = $this->createFromPhpDocNode($phpDocNode, new BetterTokenIterator([]), $node);
        // multiline by default
        $phpDocInfo->makeMultiLined();
        return $phpDocInfo;
    }
    /**
     * @param Comment[]|Doc[] $comments
     */
    private function storePreviousDocs(Node $node, array $comments, Doc $doc) : void
    {
        $previousDocsAsComments = [];
        $newMainDoc = null;
        foreach ($comments as $comment) {
            // On last Doc, stop
            if ($comment === $doc) {
                break;
            }
            // pure comment
            if (!$comment instanceof Doc) {
                $previousDocsAsComments[] = $comment;
                continue;
            }
            // make Doc as comment Doc that not last
            $previousDocsAsComments[] = new Comment($comment->getText(), $comment->getStartLine(), $comment->getStartFilePos(), $comment->getStartTokenPos(), $comment->getEndLine(), $comment->getEndFilePos(), $comment->getEndTokenPos());
            /**
             * Make last Doc before main Doc to candidate main Doc
             * so it can immediatelly be used as replacement of Main doc when main doc removed
             */
            $newMainDoc = $comment;
        }
        $node->setAttribute(AttributeKey::PREVIOUS_DOCS_AS_COMMENTS, $previousDocsAsComments);
        $node->setAttribute(AttributeKey::NEW_MAIN_DOC, $newMainDoc);
    }
    /**
     * Needed for printing
     */
    private function setPositionOfLastToken(PhpDocNode $phpDocNode) : void
    {
        if ($phpDocNode->children === []) {
            return;
        }
        $phpDocChildNodes = $phpDocNode->children;
        $phpDocChildNode = \array_pop($phpDocChildNodes);
        $startAndEnd = $phpDocChildNode->getAttribute(PhpDocAttributeKey::START_AND_END);
        if ($startAndEnd instanceof StartAndEnd) {
            $phpDocNode->setAttribute(PhpDocAttributeKey::LAST_PHP_DOC_TOKEN_POSITION, $startAndEnd->getEnd());
        }
    }
    private function createFromPhpDocNode(PhpDocNode $phpDocNode, BetterTokenIterator $betterTokenIterator, Node $node) : \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo
    {
        $this->phpDocNodeMapper->transform($phpDocNode, $betterTokenIterator);
        $phpDocInfo = new \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo($phpDocNode, $betterTokenIterator, $this->staticTypeMapper, $node, $this->annotationNaming, $this->currentNodeProvider, $this->rectorChangeCollector, $this->phpDocNodeByTypeFinder);
        $node->setAttribute(AttributeKey::PHP_DOC_INFO, $phpDocInfo);
        return $phpDocInfo;
    }
}
