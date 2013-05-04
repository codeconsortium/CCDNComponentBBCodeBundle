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
	protected $nodeParent;
	protected $nodePrevious;
	protected $nodeNext;
	
	public function setNodeParent(NodeInterface $node)
	{
		$this->nodeParent = $node;
	}
	public function getNodeParent()
	{
		return $this->nodeParent;
	}
	
	public function hasNodeParent()
	{
		return $this->nodeParent ? true : false;
	}
	
	public function setNodePrevious(NodeInterface $node)
	{
		$this->nodePrevious = $node;
	}
	public function getNodePrevious()
	{
		return $this->nodePrevious;
	}
	public function hasNodePrevious()
	{
		return $this->nodePrevious ? true : false;
	}
	
	public function setNodeNext(NodeInterface $node)
	{
		$this->nodeNext = $node;
	}
	public function getNodeNext()
	{
		return $this->nodeNext;
	}
	public function hasNodeNext()
	{
		return $this->nodeNext ? true : false;
	}
}