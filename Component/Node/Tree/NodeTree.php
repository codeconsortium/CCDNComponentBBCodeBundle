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
	protected $nodes;
	protected $index = 0;
	
	public function __construct()
	{
		$this->nodes = array();
	}
	
	public static function isTree()
	{
		return true;
	}
	
	public function getNodes()
	{
		return $this->nodes;
	}
	
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
	
	public function getNodeFirst()
	{
		if ($this->index > 0) {
			return $this->nodes[0];			
		} else {
			return null;
		}
	}
	
	public function getNodeLast()
	{
		if ($this->index > 0) {
			return $this->nodes[$this->index];
		} else {
			return null;
		}
	}
	
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
	
	public function cascadeRender()
	{
		$output = '';
		
		foreach ($this->nodes as $node) {
			$output .= $node->cascadeRender();
		}
		
		return $output;
	}
	
	public function dump()
	{
		$out = '<br><ol>';
		
//		$out .= '<li><strong>Tree</strong></li>';
		$out .= '<li><strong>Has Parent?</strong> ' . $this->hasNodeParent() . '</li>';
		
		foreach ($this->nodes as $node) {
			$out .= '<li>' . $node->dump() . '</li>';
		}
		
		$out .= '</ol>';
		
		return $out;
	}
	
	public function dumpDie()
	{
		echo $this->dump();
		
		die();
	}
}
