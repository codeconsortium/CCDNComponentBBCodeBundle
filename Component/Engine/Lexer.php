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
	 * @param Array $lexemes, string $lookup
	 * @return string $lookup
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
						'lookup_str' 	=> $lookup,
						'lexeme_key'	=> $lexeme_key,
						'token_key'		=> $token_key,
					);
				}
			}
		}
		
		return $lookup;
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param Array $tree, int $depth
	 * @return Array $tree[]
	 */
	private function &get_symbol_tree_branch(&$branch, $depth)
	{				
		if ($depth < 1)
		{
			return $branch;
		} else {
			$branch_size = count($branch);
			
			if ($branch_size < 1)
			{
				$branch[] = array();
				$branch_size++;
			}

			if ( ! is_array($branch[$branch_size - 1]))
			{
				$branch[] = array();
				$branch_size++;
			}
			
			return $this->get_symbol_tree_branch($branch[$branch_size - 1], --$depth);
		}		
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param Array $branch, string $lookup
	 * @return int $leaf_key | null
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
	 * @access private
	 * @param Array $symbol, Array $lexeme
	 * @return string|null
	 */
	private function add_param_for_tag(&$symbol, &$lexeme)
	{
		$lookup_str = $symbol['lookup_str'];
		$count = strlen($lexeme['symbol_lexeme']);
		
		// /(\[)|(\=)|(\])/
		$regex = '/(\[([a-zA-Z0-9]{0,' . $count . '})\=)|(\])/';
		
		$param = preg_split($regex, $lookup_str, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

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
					foreach($lexeme['param_choices'] as $param_choice_key => $param_choice)
					{
						if ($param[2] == $param_choice_key)
						{
							$symbol['tag_param'] = $param_choice;

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
	 * @param Array $scanTree, Array $lexemes
	 * @return Array $symbol_tree
	 */
	public function &process(&$scan_tree, &$lexemes)
	{
		$symbol_tree = array();
		$symbol_tree_depth = 0;
		
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
				if ($lexemes[$lookup['lexeme_key']]['token_count'] > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$branch = &$this->get_symbol_tree_branch($symbol_tree, $symbol_tree_depth);
						
						// find the parent tag
						$parent_leaf_key = $this->find_my_parent($branch, $lookup);
						
						if ($parent_leaf_key !== null)
						{
							// get the tags params
							$this->add_param_for_tag(&$lookup, &$lexemes[$lookup['lexeme_key']]);
							$this->add_param_for_tag(&$branch[$parent_leaf_key], &$lexemes[$branch[$parent_leaf_key]['lexeme_key']]);

							// interconnect associative lexeme
							$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
							$lookup['validation_token'] = $token;
							$lookup['ref_parent'] = $parent_leaf_key;
							
							// add the lookup array to the branch
							$branch[] = $lookup;
							
							$branch[$parent_leaf_key]['validation_token'] = $token;
							$branch[$parent_leaf_key]['ref_child'] = (count($branch) - 1);
							
							// nested tags get put in nested arrays.
							$symbol_tree_depth--;
							
							continue;
						}
					// *******************************************************
					// 		OPENING TAG
					// *******************************************************
					} else {
						// we go to next nested level
						$symbol_tree_depth++;
						
						$branch = &$this->get_symbol_tree_branch($symbol_tree, $symbol_tree_depth);

						$branch[] = $lookup;
						
						continue;
					} // end check for determining open or closing tags
				// *******************************************************
				//		SINGULAR TAGS
				// *******************************************************	
				} else {
					// token is a lonewolf type, not matchable!
					$branch = &$this->get_symbol_tree_branch($symbol_tree, $symbol_tree_depth);
					
					$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
					$lookup['validation_token'] = $token;
					$branch[] = $lookup;
					
					continue;
				} // end token matchable type check
			}

			// correct root tree depth if drops below zero
			if ($symbol_tree_depth < 0)	{ $symbol_tree_depth = 0; }

			$branch = &$this->get_symbol_tree_branch($symbol_tree, $symbol_tree_depth);
			
			$branch[] = $scan_tree[$scan_key];
		}
		
		return $symbol_tree;
	}
	
	

	/**
	 *
	 * @access private
	 * @param Array $branch
	 * @return string $output
	 */
	private function parse_nested_content(&$branch)
	{
		$output = '';
		
		if (is_array($branch))
		{
			foreach ($branch as $leaf_key => $leaf)
			{
				if (is_array($leaf))
				{
					if (array_key_exists('lookup_str', $leaf))
					{
						$output .= $leaf['lookup_str'];						
					} else {					
						$output .= $this->parse_nested_content(&$leaf);
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
	 * @param Array $branch, int $ref_parent, int $ref_child
	 */
	private function collapse_invalid_nested(&$branch, $ref_parent, $ref_child)
	{
		$branchSize = count($branch) - 1;
		$content = '';

		if ($branchSize > 1)
		{
			for ($key = $ref_parent + 1; $key < $ref_child; $key++)
			{
				$content .= $this->parse_nested_content(&$branch[$key]);
			}

			$branch[$ref_parent + 1] = $content;
			
			for ($key = $ref_parent + 2; $key < $ref_child; $key++)
			{
				unset($branch[$key]);
			}
		}
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param Array $symbol_tree, Array $lexeme_table
	 */
	public function post_process(&$symbol_tree, &$lexeme_table)
	{
		foreach($symbol_tree as $symbol_key => $symbol)
		{
			if (is_array($symbol))
			{
				// if nesting is not allowed inside of this lexeme then parse it to the
				// collapsing method to crush it down into its original unlexed form.
				if (array_key_exists('lexeme_key', $symbol) && array_key_exists('validation_token', $symbol)
				&&	array_key_exists('token_key', $symbol) && array_key_exists('ref_child', $symbol))
				{
					if (array_key_exists('use_nested', $lexeme_table[$symbol['lexeme_key']]) && $symbol['lexeme_key']['token_key'] == 0)
					{
						if ($lexeme_table[$symbol['lexeme_key']]['use_nested'] == false)
						{						
							$this->collapse_invalid_nested(&$symbol_tree, $symbol_key, $symbol['ref_child']);
						}
					}
				} else {
					$this->post_process(&$symbol_tree[$symbol_key], &$lexeme_table);
				}
			}
		}		
	}
	
}
