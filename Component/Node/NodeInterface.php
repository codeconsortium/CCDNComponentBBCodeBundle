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

namespace CCDNComponent\BBCodeBundle\Component\Node;

//use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeInterface;

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
 * @abstract
 *
 */
interface NodeInterface
{
	/**
	 * 
	 * Set the node that owns this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodeParent(NodeInterface $node);
	
	/**
	 * 
	 * Get the node that owns this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeParent();
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodeParent();
	
	/**
	 * 
	 * Set the node that precedes this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodePrevious(NodeInterface $node);
	
	/**
	 * 
	 * Get the node that precedes this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodePrevious();
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodePrevious();
	
	/**
	 * 
	 * Set the node that follows this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodeNext(NodeInterface $node);
	
	/**
	 * 
	 * Get the node that follows this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeNext();
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodeNext();
}