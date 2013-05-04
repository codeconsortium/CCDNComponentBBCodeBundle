<?php

/*
 * This file is part of the CCDNComponent BBCodeBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNComponent\BBCodeBundle\Component\Node\Tree;

use CCDNComponent\BBCodeBundle\Component\Node\NodeInterface;
use CCDNComponent\BBCodeBundle\Component\Node\Tree\NodeTreeInterface;
use CCDNComponent\BBCodeBundle\Component\Node\NodeBase;

/**
 *
 * @category CCDNComponent
 * @package  BBCodeBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNComponentBBCodeBundle
 *
 */
class NodeTree extends NodeBase implements NodeTreeInterface, NodeInterface
{
    /**
     *
     * An array of nodes, may be lexemes or more
     * nodetrees all implementing NodeInterface.
     *
     * @var array $nodes
     */
    protected $nodes;

    /**
     *
     * A manual counter we increase for each node, to
     * save doing a count() call on the node array.
     *
     * @var int $index
     */
    protected $index = 0;

    public function __construct()
    {
        $this->nodes = array();
    }

    /**
     *
     * As we extend the NodeBase, we must state if we are
     * a tree node or a lexeme node, which is important
     * during both validation and rendering cascading.
     *
     * @access public
     * @return bool
     */
    public static function isTree()
    {
        return true;
    }

    /**
     *
     * Returns the nodes array.
     *
     * @access public
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     *
     * Add a new object implementing NodeInterface to the node array.
     *
     * @access public
     * @param NodeInterface $node
     */
    public function addNode(NodeInterface $node)
    {
        $this->nodes[] = $node;

        $node->setNodeParent($this);

        if (! $node::isTree() && $this->index != 0) {
            if ($this->nodeMatchesFirst($node)) {

                $id = uniqid();

                $this->nodes[0]->setMatchingNode($node, $id);
                $node->setMatchingNode($this->nodes[0], $id);
            }

            $node->setNodePrevious($this->nodes[$this->index]);
        }

        $this->nodes[$this->index]->setNodeNext($node);

        $this->index++;
    }

    /**
     *
     * Compare the specified $node against the first
     * node in this tree. Matches will be assumed by
     * the values returned by both CanonicalLexemeNames.
     *
     * @access public
     * @param NodeInterface $node
     */
    public function nodeMatchesFirst(NodeInterface $node)
    {
        $first = $this->nodes[0];

        if (! $first::isTree() && ! $node::isTree()) {
            if ($first::getCanonicalLexemeName() == $node::getCanonicalLexemeName()) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * Returns the first node of the array.
     *
     * @access public
     * @return NodeInterface
     */
    public function getNodeFirst()
    {
        if ($this->index > 0) {
            return $this->nodes[0];
        } else {
            return null;
        }
    }

    /**
     *
     * Returns the last node of the array.
     *
     * @access public
     * @return NodeInterface
     */
    public function getNodeLast()
    {
        if ($this->index > 0) {
            return $this->nodes[$this->index];
        } else {
            return null;
        }
    }

    /**
     *
     * Cascades the validation process through each node.
     * Sub NodeTrees will cascade further, and Lexemes
     * will self validate.
     *
     * @access public
     * @param NodeInterface $parentNode
     */
    public function cascadeValidate(NodeInterface $parentNode = null)
    {
        foreach ($this->nodes as $node) {
            if (! $node::isTree()) {
                $node->cascadeValidate($parentNode);
            }
        }

        $fn = $this->getNodeFirst();

        if ($fn->isValid(true)) {
            $lastValid = $fn;
        } else {
            $lastValid = $parentNode;
        }

        foreach ($this->nodes as $node) {
            if ($node::isTree()) {
                $node->cascadeValidate($lastValid);
            }
        }
    }

    /**
     *
     * Cascades the rendering process through each node.
     * Sub NodeTrees will cascade further, and Lexemes
     * will self render.
     *
     * @access public
     * @return string
     */
    public function cascadeRender()
    {
        $output = '';

        foreach ($this->nodes as $node) {
            $output .= $node->cascadeRender();
        }

        return $output;
    }

    /**
     *
     * Cascades dumping process through each node.
     * Sub NodeTrees will cascade further, and Lexemes
     * will self dump.
     *
     * Use this for debugging purposes ONLY!
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        $out = '<br><ol>';

        //$out .= '<li><strong>Tree</strong></li>';
        //$out .= '<li><strong>Has Parent?</strong> ' . $this->hasNodeParent() . '</li>';

        foreach ($this->nodes as $node) {
            $out .= '<li>' . $node->dump() . '</li>';
        }

        $out .= '</ol>';

        return $out;
    }

    /**
     *
     * Entry point for cascading array dump.
     *
     * @access public
     * @return string
     */
    public function dumpDie()
    {
        echo $this->dump();

        die();
    }
}
