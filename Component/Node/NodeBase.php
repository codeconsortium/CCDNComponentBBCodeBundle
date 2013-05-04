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
 * @abstract
 *
 */
abstract class NodeBase implements NodeInterface
{
	/**
	 * 
	 * The node that owns this node.
	 * 
	 * @var NodeInterface $nodeParent
	 */
	protected $nodeParent;
	
	/**
	 * 
	 * The node that precedes this node.
	 * 
	 * @var NodeInterface $nodePrevious
	 */
	protected $nodePrevious;
	
	/**
	 * 
	 * The node that follows this node.
	 * 
	 * @var NodeInterface $nodeNext
	 */
	protected $nodeNext;
	
	/**
	 * 
	 * Set the node that owns this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodeParent(NodeInterface $node)
	{
		$this->nodeParent = $node;
	}
	
	/**
	 * 
	 * Get the node that owns this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeParent()
	{
		return $this->nodeParent;
	}
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodeParent()
	{
		return $this->nodeParent ? true : false;
	}
	
	/**
	 * 
	 * Set the node that precedes this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodePrevious(NodeInterface $node)
	{
		$this->nodePrevious = $node;
	}
	
	/**
	 * 
	 * Get the node that precedes this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodePrevious()
	{
		return $this->nodePrevious;
	}
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodePrevious()
	{
		return $this->nodePrevious ? true : false;
	}
	
	/**
	 * 
	 * Set the node that follows this node.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 */
	public function setNodeNext(NodeInterface $node)
	{
		$this->nodeNext = $node;
	}
	
	/**
	 * 
	 * Get the node that follows this node.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getNodeNext()
	{
		return $this->nodeNext;
	}
	
	/**
	 * 
	 * @access public
	 * @param bool
	 */
	public function hasNodeNext()
	{
		return $this->nodeNext ? true : false;
	}
}