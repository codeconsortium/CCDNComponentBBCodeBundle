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
interface NodeTreeInterface
{
	public function __construct();
	
	/**
	 * 
	 * As we extend the NodeBase, we must state if we are
	 * a tree node or a lexeme node, which is important
	 * during both validation and rendering cascading.
	 * 
	 * @access public
	 * @return bool
	 */
	public static function isTree();
	
	/**
	 * 
	 * Returns the nodes array.
	 * 
	 * @access public
	 * @return array
	 */
	public function getNodes();
	
	/**
	 * 
	 * Add a new object implementing NodeInterface to the node array.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function addNode(NodeInterface $node);
	
	/**
	 * 
	 * Compare the specified $node against the first
	 * node in this tree. Matches will be assumed by
	 * the values returned by both CanonicalLexemeNames.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function nodeMatchesFirst(NodeInterface $node);
	
	/**
	 * 
	 * Returns the first node of the array.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeFirst();
	
	/**
	 * 
	 * Returns the last node of the array.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeLast();
	
	/**
	 * 
	 * Cascades the validation process through each node.
	 * Sub NodeTrees will cascade further, and Lexemes
	 * will self validate.
	 * 
	 * @access public
	 * @param NodeInterface $parentNode
	 */
	public function cascadeValidate(NodeInterface $parentNode = null);
	
	/**
	 * 
	 * Cascades the rendering process through each node.
	 * Sub NodeTrees will cascade further, and Lexemes
	 * will self render.
	 * 
	 * @access public
	 * @return string
	 */
	public function cascadeRender();
	
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
	public function dump();
	
	/**
	 * 
	 * Entry point for cascading array dump.
	 * 
	 * @access public
	 * @return string
	 */
	public function dumpDie();
}
