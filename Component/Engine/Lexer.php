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
 *
 * Note: use of ENT_SUBSTITUTE in htmlentities requires PHP 5.4.0, and so
 * PHP versions below won't use it, so it was commented out, and can be
 * uncommented if you are using PHP 5.4.0 and above only.
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
	 * @access private
	 * @param array $lexemes, string $lookup
	 * @return string $lookup
	 */
	private function lookupLexeme(&$lexemes, $lookup)
	{
		foreach ($lexemes as $lexemeKey => $lexeme)
		{
			foreach ($lexeme['symbol_token'] as $tokenKey => $token)
			{
				if (preg_match($token, $lookup))
				{
					return array(
						'lookup_str' 	=> $lookup,
						'lexeme_key'	=> $lexemeKey,
						'token_key'		=> $tokenKey,
					);
				}
			}
		}
		
		return $lookup;
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param array $tree, int $depth
	 * @return array $tree
	 */
	private function &getSymbolTreeBranch(&$branch, $depth)
	{				
		if ($depth < 1)
		{
			return $branch;
		} else {
			$branchSize = count($branch);
			
			if ($branchSize < 1)
			{
				$branch[] = array();
				$branchSize++;
			}

			if ( ! is_array($branch[$branchSize - 1]))
			{
				$branch[] = array();
				$branchSize++;
			}
			
			return $this->getSymbolTreeBranch($branch[$branchSize - 1], --$depth);
		}		
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param array $branch, string $lookup
	 * @return int $leafKey | null
	 */
	private function findMyParent(&$branch, $lookup)
	{
		$leafCount = count($branch);
		
		for($leafKey = --$leafCount; $leafKey >= 0; $leafKey--)
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
	 * @access private
	 * @param array $symbol, array $lexeme
	 * @return string|null
	 */
	private function addParamForTag(&$symbol, &$lexeme)
	{
		$lookupStr = $symbol['lookup_str'];
		$count = strlen($lexeme['symbol_lexeme']);
		
		// /(\[)|(\=)|(\])/
		$regex = '/(\[([a-zA-Z0-9]{0,' . $count . '})\=)|(\])/';
		
		$param = preg_split($regex, $lookupStr, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

		if (is_array($param) && count($param) > 2)
		{
			$len = strlen($param[0]);
			
			if (substr($param[0], $len - 1, $len)  == '=')
			{
				
				// this section is for tags with multiple choices for a param,
				// in such cases the param provided must match one from the 
				// list provided for that bb tag type.
				if (array_key_exists('param_choices', $lexeme))
				{
					foreach($lexeme['param_choices'] as $paramChoiceKey => $paramChoice)
					{
						if ($param[2] == $paramChoiceKey)
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
						$protocol = preg_split('/(http|https|ftp)/', $param[2], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
					
						if ($protocol[0] == "http" || $protocol[0] == "https" || $protocol[0] == "ftp")
						{
							$symbol['tag_param'] = $param[2];
						} else {
							$symbol['tag_param'] = 'http://' . $param[2];
						}
						
						return;
					}
				} 
				
				$symbol['tag_param'] = $param[2];
			}
		}		
	}
	

	
	/**
	 *
	 * @access public
	 * @param array $scanTree, array $lexemes
	 * @return array $symbolTree
	 */
	public function &process(&$scanTree, &$lexemes)
	{
		$symbolTree = array();
		$symbolTreeDepth = 0;
		
		$scanTreeSize = count($scanTree);

		for ($scanKey = 0; $scanKey < $scanTreeSize; $scanKey++)
		{
			$lookup = $this->lookupLexeme($lexemes, $scanTree[$scanKey]);
			
			if (is_array($lookup))
			{	
				// *******************************************************
				//		PAIRED TAGS
				// *******************************************************
				// does token have matching closing tokens in the lexemes? 
				// (could be a one off, like a smiley [i.e; no closing tag required])
				if ($lexemes[$lookup['lexeme_key']]['token_count'] > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$branch = &$this->getSymbolTreeBranch($symbolTree, $symbolTreeDepth);
						
						// find the parent tag
						$parentLeafKey = $this->findMyParent($branch, $lookup);
						
						if ($parentLeafKey !== null)
						{
							// get the tags params
							$this->addParamForTag($lookup, $lexemes[$lookup['lexeme_key']]);
							$this->addParamForTag($branch[$parentLeafKey], $lexemes[$branch[$parentLeafKey]['lexeme_key']]);

							// interconnect associative lexeme
							$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
							$lookup['validation_token'] = $token;
							$lookup['ref_parent'] = $parentLeafKey;
							
							// add the lookup array to the branch
							$branch[] = $lookup;
							
							$branch[$parentLeafKey]['validation_token'] = $token;
							$branch[$parentLeafKey]['ref_child'] = (count($branch) - 1);
							
							// nested tags get put in nested arrays.
							$symbolTreeDepth--;
							
							continue;
						}
					// *******************************************************
					// 		OPENING TAG
					// *******************************************************
					} else {
						// we go to next nested level
						$symbolTreeDepth++;
						
						$branch = &$this->getSymbolTreeBranch($symbolTree, $symbolTreeDepth);

						$branch[] = $lookup;
						
						continue;
					} // end check for determining open or closing tags
				// *******************************************************
				//		SINGULAR TAGS
				// *******************************************************	
				} else {
					// token is a lonewolf type, not matchable!
					$branch = &$this->getSymbolTreeBranch($symbolTree, $symbolTreeDepth);
					
					$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
					$lookup['validation_token'] = $token;
					$branch[] = $lookup;
					
					continue;
				} // end token matchable type check
			}

			// correct root tree depth if drops below zero
			if ($symbolTreeDepth < 0)	{ $symbolTreeDepth = 0; }

			$branch = &$this->getSymbolTreeBranch($symbolTree, $symbolTreeDepth);
			
			$branch[] = $scanTree[$scanKey];
		}
		
		return $symbolTree;
	}
	
	

	/**
	 *
	 * @access private
	 * @param array $branch
	 * @return string $output
	 */
	private function parseNestedContent(&$branch)
	{
		$output = '';
		
		if (is_array($branch))
		{
			foreach ($branch as $leafKey => $leaf)
			{
				if (is_array($leaf))
				{
					if (array_key_exists('lookup_str', $leaf))
					{
						$output .= $leaf['lookup_str'];						
					} else {					
						$output .= $this->parseNestedContent($leaf);
					}
				} else {
					$output .= $leaf;
				}
			}	
		} else {
			$output .= $branch;
		}
		
		return $output;
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param array $branch, int $refParent, int $refChild
	 */
	private function collapseInvalidNested(&$branch, $refParent, $refChild)
	{
		$branchSize = count($branch) - 1;
		$content = '';

		if ($branchSize > 1)
		{
			for ($key = $refParent + 1; $key < $refChild; $key++)
			{
				$content .= $this->parseNestedContent($branch[$key]);
			}

			$branch[$refParent + 1] = $content;
			
			for ($key = $refParent + 2; $key < $refChild; $key++)
			{
				unset($branch[$key]);
			}
		}
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param array $symbolTree, array $lexemeTable
	 */
	public function postProcess(&$symbolTree, &$lexemeTable)
	{
		foreach($symbolTree as $symbolKey => $symbol)
		{
			if (is_array($symbol))
			{
				// if nesting is not allowed inside of this lexeme then parse it to the
				// collapsing method to crush it down into its original unlexed form.
				if (array_key_exists('lexeme_key', $symbol) && array_key_exists('validation_token', $symbol)
				&&	array_key_exists('token_key', $symbol) && array_key_exists('ref_child', $symbol))
				{
					if (array_key_exists('use_nested', $lexemeTable[$symbol['lexeme_key']]) && $symbol['lexeme_key']['token_key'] == 0)
					{
						if ($lexemeTable[$symbol['lexeme_key']]['use_nested'] == false)
						{						
							$this->collapseInvalidNested($symbolTree, $symbolKey, $symbol['ref_child']);
						}
					}
				} else {
					$this->postProcess($symbolTree[$symbolKey], $lexemeTable);
				}
			}
		}		
	}
	
}
