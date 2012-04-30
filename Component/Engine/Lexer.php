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
	 * @param $lexemes, $lookup
	 * @return $lookup
	 */
	private function lookup_lexeme(&$lexemes, $lookup)
	{
		foreach ($lexemes as $lexeme_key => $lexeme)
		{
			foreach ($lexeme['symbol_token'] as $token_key => $token)
			{
				if (preg_match($token, $lookup))
				{
					return array(
						'lookup_str' => $lookup,
	/*					'symbol_lexeme' => &$lexeme['symbol_lexeme'],*/
						'symbol_token' => &$lexeme['symbol_token'][$token_key],
						'symbol_html' => &$lexeme['symbol_html'][$token_key],
						'lexeme_key' => $lexeme_key,
						'token_key' => $token_key,
						'original_lexeme' => &$lexeme,
					);
				}
			}
		}
		
		return $lookup;
	}
	
	
	/**
	 *
	 * @access private
	 * @param $tree, $depth
	 * @return $tree[]
	 */
	private function &get_lexeme_tree_branch(&$tree, $depth)
	{
		$tree_size = count($tree);	
		$tree_size--;
	
		if ($tree_size < 0)
		{
			// no indices, create first one
			$tree[++$tree_size] = array();
		} else {
			// if last indice is not an array then we need to create one
			if ( ! is_array($tree[$tree_size]))
			{
				$tree[++$tree_size] = array();
			} else {
				// if it is an array make sure its not a lexeme
				if (array_key_exists('lexeme_key', $tree[$tree_size]))
				{
					$tree[++$tree_size] = array();
				}
			}
		}

		// check if we have reached depth 0 which
		// is where we want we want to return
		if ($depth < 1)
		{
			return $tree[$tree_size];
		} else {
			return $this->get_lexeme_tree_branch($tree[$tree_size] , --$depth);
		}
	}


	/**
	 *
	 * @access private
	 * @param $branch, $lookup
	 * @return $leaf_key | null
	 */
	private function find_my_parent(&$branch, $lookup)
	{
		$leaf_count = count($branch);
		
		for($leaf_key = --$leaf_count; $leaf_key >= 0; $leaf_key--)
		{
			if (is_array($branch[$leaf_key]))
			{
				if (array_key_exists('lexeme_key', $branch[$leaf_key]))
				{
					if ($branch[$leaf_key]['lexeme_key'] == $lookup['lexeme_key'] && $branch[$leaf_key]['token_key'] == 0)
					{
						if ( ! array_key_exists('ref_parent', $branch[$leaf_key]))
						{
							return $leaf_key;
						}
					}
				}
			}
		}
		
		return null;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $scanTree, $lexemes
	 * @return $lexemeTree
	 */
	public function process(&$scan_tree, &$lexemes)
	{
		$lexeme_tree = array();
		$lexeme_tree_depth = 0;

		$scan_tree_size = count($scan_tree);

		for ($scan_key = 0; $scan_key < $scan_tree_size; $scan_key++)
		{
			$lookup = $this->lookup_lexeme($lexemes, $scan_tree[$scan_key]);
			
			if (is_array($lookup))
			{	
				// *******************************************************
				//		PAIRED TAGS
				// *******************************************************
				// does token have matching closing tokens in the lexemes? 
				// (could be a one off, like a smiley [i.e; no closing tag required])
				if (count($lookup['original_lexeme']['symbol_token']) > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$branch = &$this->get_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
						
						// find the parent tag
						$parent_leaf_key = $this->find_my_parent($branch, $lookup);
						
						if ($parent_leaf_key !== null)
						{
							// interconnect associative lexeme
							$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
							$lookup['validation_token'] = $token;
							$lookup['ref_parent'] = &$branch[$parent_leaf_key];
							// add the lookup array to the branch
							$branch[] = $lookup;
							
							$branch[$parent_leaf_key]['validation_token'] = $token;
							$branch[$parent_leaf_key]['ref_child'] = &$branch[(count($branch) - 1)];
							
							// nested tags get put in nested arrays.
							$lexeme_tree_depth--;
							
							continue;
						} else {
							
						//	$lexeme_tree_depth--;
						}
						
					// *******************************************************
					// 		OPENING TAG
					// *******************************************************
					} else {
						// we go to next nested level
						// deeper down the rabbit hole we go.
						$lexeme_tree_depth++;
						
						$branch = &$this->get_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);

						$branch[] = $lookup;
						
						continue;
					} // end check for determining open or closing tags
				// *******************************************************
				//		SINGULAR TAGS
				// *******************************************************	
				} else {
					// token is a lonewolf type, not matchable!
					$branch = &$this->get_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
					$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
					$lookup['validation_token'] = $token;
					$branch[] = $lookup;
					
					continue;
				} // end token matchable type check
			}

			// correct root tree depth if drops below zero
			if ($lexeme_tree_depth < 0)	{ $lexeme_tree_depth = 0; }

			$branch = &$this->get_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);

			$branch[] = $scan_tree[$scan_key];
		}
		
		return $lexeme_tree;
	}
	
}
