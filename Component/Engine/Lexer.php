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

/*
 * Created by Reece Fowell 
 * <me at reecefowell dot com> 
 * <reece at codeconsortium dot com>
 * Created on 17/12/2011
 */

namespace CCDNComponent\BBCodeBundle\Component\Engine;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class Lexer
{
	
	/**
	 *
	 * @access protected
	 */
	protected $lexemes;

	/**
	 *
	 * @access protected
	 */
	protected $scanTree;

	/**
	 *
	 * @access protected
	 */
	protected $scanTreeKey;

	/**
	 *
	 * @access protected
	 */
	protected $scanTreeSize;

	/**
	 *
	 * @access protected
	 */
	protected $symbolTreeDepth;
	
	/**
	 *
	 * @access protected
	 */
	protected $lexemeTable;
	
	/**
	 *
	 * @access public
	 * @param LexemeTable $lexemeTable
	 */
	public function setLexemeTable($lexemeTable)
	{
		$this->lexemeTable = $lexemeTable;
	}
	
	/**
	 *
	 * @access public
	 * @param &$scanTree
	 */
	public function &process(&$scanTree)
	{
		$this->scanTree = $scanTree;
		$this->scanTreeKey = 0;
		$this->scanTreeSize = count($scanTree);
		
		$this->symbolTreeDepth = 0;
		
		$symbolTree = $this->processLexing(null);
		$this->processInvalidNesting($symbolTree, $tmp);
		
		return $symbolTree;
	}

	/**
	 *
	 * @access protected
	 * @param array $symbol, array $lexeme
	 * @return string|null
	 */
	protected function addParamForTag(&$symbol, &$lexeme)
	{
		$lookupStr = $symbol['lookup_str'];
		$count = strlen($lexeme['symbol_lexeme']);
		
		$regex = '/^(\[\/?[A-Z]{1,' . $count . '}="([ _,.?!@#$%&*()^=\+\-\'\/\w]*)"{0,500}?:?\])$/';
		
		$param = preg_split($regex, $lookupStr, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

		if (is_array($param) && count($param) > 1)
		{
			// This section is for tags with multiple choices for a param, in such cases the
			// param provided must match one from the list provided for that bb tag type.
			if (array_key_exists('param_choices', $lexeme))
			{
				foreach($lexeme['param_choices'] as $paramChoiceKey => $paramChoice)
				{
					if ($param[1] == $paramChoiceKey)
					{
						$symbol['tag_param'] = $paramChoice;

						return;
					}
				}
				
				return;
			}

			if (array_key_exists('param_is_url', $lexeme))
			{
				if ($lexeme['param_is_url'] == true)
				{			
					$protocol = preg_split('/^(http|https|ftp)\:\/\//', $param[1], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
				
					if ($protocol[0] == 'http' || $protocol[0] == 'https' || $protocol[0] == 'ftp')
					{
						$symbol['tag_param'] = $param[1];
					} else {
						$symbol['tag_param'] = 'http://' . $param[1];
					}
					
					return;
				}
			} 
			
			$symbol['tag_param'] = $param[1];
		}		
	}

	/**
	 *
	 * @access protected
	 * @param array $branch, string $lookup
	 * @return int $leafKey | null
	 */
	protected function findMyParent($branch, $lookup)
	{
		$leafCount = count($branch);

		for ($leafKey = --$leafCount; $leafKey >= 0; $leafKey--)
		{		 
			if (is_array($branch[$leafKey]))
			{
				if (array_key_exists('lexeme_key', $branch[$leafKey]))
				{
					if ($branch[$leafKey]['lexeme_key'] == $lookup['lexeme_key'] && $branch[$leafKey]['token_key'] == 0)
					{
						if ( ! array_key_exists('ref_parent', $branch[$leafKey]))
						{
							return $leafKey;
						}
					}
				}
			}
		}
		
		return null;
	}
		
	/**
	 *
	 * @access protected
	 * @param $lexeme
	 * @return bool
	 */
	protected function scanAheadForChild($lexeme)
	{	
		$scanAheadKey = $this->scanTreeKey + 1;
		
		for (; $this->scanTreeKey < $this->scanTreeSize; $scanAheadKey++)
		{
			$lookup = $this->lexemeTable->lookup($this->scanTree[$scanAheadKey]);
			
			if (is_array($lookup))
			{
				$lexemeAhead = $this->lexemeTable->getLexeme($lookup['lexeme_key']);	
				
				if ($lookup['token_key'] == 1)
				{
					if ($lexemeAhead['symbol_lexeme'] == $lexeme['symbol_lexeme'])
					{
						return true;
					} else {
						return false;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 *
	 * @access protected
	 * @param array $lookup
	 * @return array $symbolTree
	 */
	protected function processLexing($lookup)
	{
		$symbolTree = array();
		
		if ($lookup != null) {
			$symbolTree[] = $lookup;
		}

		for (; $this->scanTreeKey < $this->scanTreeSize;$this->scanTreeKey++)
		{			
			$lookup = $this->lexemeTable->lookup($this->scanTree[$this->scanTreeKey]);
			
			if (is_array($lookup))
			{
				$lexeme = $this->lexemeTable->getLexeme($lookup['lexeme_key']);
				
				// Paired Tags
				if ($lexeme['token_count'] > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$parentLeafKey = $this->findMyParent($symbolTree, $lookup);

						if ($parentLeafKey !== null)
						{
							$parentSymbol =& $symbolTree[$parentLeafKey];
							$parentLexeme = $this->lexemeTable->getLexeme($parentSymbol['lexeme_key']);
							
							if (array_key_exists('accepts_param', $lexeme))
							{
								// get the tags params
								$this->addParamForTag($lookup, $lexeme);
								$this->addParamForTag($parentSymbol, $parentLexeme);
							}

							// Establish associative lexeme relationship.
							$validationToken = '__' . uniqid() . '__';
							
							$lookup['validation_token'] = $validationToken;
							$lookup['ref_parent'] = $parentLeafKey;
							
							// add the lookup array to the branch
		    				$symbolTree[] = $lookup;

							$parentSymbol['validation_token'] = $validationToken;
							$parentSymbol['ref_child'] = (count($symbolTree) - 1);
							
							if ($this->symbolTreeDepth > 0) {
			    				$this->symbolTreeDepth--;
		    						    				
			    				return $symbolTree;
			    			}
			
							continue;
						}
					// *******************************************************
					// 		OPENING TAG
					// *******************************************************
					} else {
						if ($this->scanAheadForChild($lexeme)) // Don't bother nesting deeper if it has no/interlaced child.
						{
							$this->symbolTreeDepth++;
							$this->scanTreeKey++; // Manually inc to avoid infinite recursion due to for(loop) post inc. 
						
							$symbolTree[] = $this->processLexing($lookup);
						
							continue;						
						}
					} // end check for determining open or closing tags
				// *******************************************************
				//		SINGULAR TAGS
				// *******************************************************	
				} else {
					// token is a lonewolf type, not matchable!				
					$token = '_' . uniqid() . '_';
					$lookup['validation_token'] = $token;

				} // end token matchable type check
			}
			
			$symbolTree[] = $lookup;			
		}
		
		return $symbolTree;
	}		
	
	/**
	 *
	 * Invalidates nested tags that are not on the allowed_nestable
	 * list for the parent tag by dropping its validation_token.
	 *
	 * @access protected
	 * @param array &$symbolTree, mixed[] $parentSymbol
	 */
	protected function processInvalidNesting(&$symbolTree, &$parentSymbol)
	{
		$lastSymbol =& $parentSymbol;
		$symbolTreeSize = count($symbolTree);

		for ($symbolKey = 0; $symbolKey < $symbolTreeSize; $symbolKey++)
		{
			$symbol =& $symbolTree[$symbolKey];
			
			if (is_array($symbol))
			{
				// We know its an array, but is it a lexeme? or nested content? And has it already been invalidated?
				if (array_key_exists('validation_token', $symbol))
				{			
					$currentLexeme = $this->lexemeTable->getLexeme($symbol['lexeme_key']);
					
					if ($parentSymbol != null && is_array($parentSymbol))
					{				
						// Check limitations of parent node?
						if (array_key_exists('validation_token', $parentSymbol))
						{
							// Does parent symbols lexeme have an allowed_nestable?
							$parentLexeme = $this->lexemeTable->getLexeme($parentSymbol['lexeme_key']);
	                    
							if (array_key_exists('allowed_nestable', $parentLexeme))
							{
								if ( ! in_array($currentLexeme['symbol_lexeme'], $parentLexeme['allowed_nestable']))
								{
									unset($symbol['validation_token']);
								
									if ($currentLexeme['token_count'] > 1)
									{
										unset($symbolTree[$symbol['ref_child']]['validation_token']);
									}
								}
							}						
						}
					}			
					
					// Opening tag? (and still valid?)
					if ($currentLexeme['token_count'] > 1 && array_key_exists('validation_token', $symbol))
					{
						if ($symbol['token_key'] == 0)
						{
							$lastSymbol =& $symbol;							
						} else { // Closing tag?
							$lastSymbol =& $parentSymbol; // Revert back to parent to test allowed_nestable against.
						}
					}
				} else {
					if ( ! array_key_exists('lexeme_key', $symbol))
					{
						// If not a lexeme, must be nested content.
						$this->processInvalidNesting($symbol, $lastSymbol);				
					}
				}
			}
		}
	}
	
}
