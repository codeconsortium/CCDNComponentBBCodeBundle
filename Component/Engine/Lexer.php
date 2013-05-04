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

namespace CCDNComponent\BBCodeBundle\Component\Engine;

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
class Lexer
{
	/**
	 *
	 * @var LexemeTable $lexemeTable
	 */
	protected static $lexemeTable;
	
	/**
	 *
	 * @var array $scanChunks
	 */
	protected static $scanChunks;
	
	/**
	 *
	 * @var int $scanChunksSize
	 */
	protected static $scanChunksSize;
	
	/**
	 *
	 * @var int $scanChunksIndex
	 */
	protected static $scanChunksIndex;
		
	/**
	 *
	 * @access public
	 * @param LexemeTable $lexemeTable
	 */
	public static function setLexemeTable($lexemeTable)
	{
		static::$lexemeTable = $lexemeTable;
	}
	
	/**
	 * 
	 * @access public
	 * @param array $scanChunks
	 * @return array
	 */
	public function process($scanChunks)
	{		
		static::$scanChunks = $scanChunks;
		static::$scanChunksSize = count($scanChunks);
		static::$scanChunksIndex = 0;
	
		$tree = $this->lexify();

		$tree->cascadeValidate();
		
		return $tree;
	}
	
	/**
	 *
	 * Iterates over the scan chunks, and recursively calls itself for each opening
	 * lexeme it matches. A return from the last recursive call takes place when
	 * both a matching lexeme (closing half) is found and that the current tree
	 * has a parent. The iterator is used in a static context to maintain iterations
	 * through recursion. Each recursive call creates a new tree that once returned
	 * is appended to a node of the last tree.
	 * 
	 * @access protected
	 * @param NodeTreeInterface $parent
	 * @param NodeInterface $node
	 */
	protected function lexify($parent = null, $node = null)
	{	
		$tree = static::$lexemeTable->createNodeTree();
		
		if ($parent) {
			$tree->setNodeParent($parent);			
		}
		
		if ($node) {
			$tree->addNode($node);
		}
		
		for (; static::$scanChunksIndex < static::$scanChunksSize; static::$scanChunksIndex++) {
			
			$scanStr = static::$scanChunks[static::$scanChunksIndex];
			
			$node = static::$lexemeTable->lookup($scanStr);
			
			if ($node::isLexable() && !$node::isStandAlone()) {

				if ($node->isOpeningTag()) {
					static::$scanChunksIndex++;

					$tree->addNode($this->lexify($tree, $node));
					
				} else {
					$tree->addNode($node);
					
					if ($node->isClosingTag()) {
						if ($tree->nodeMatchesFirst($node)) {							
							if ($tree->hasNodeParent()) {
								return $tree;
							}
						}
					}
				}
			} else {
				$tree->addNode($node);
			}			
		} // endfor
		
		return $tree;
	}
}
