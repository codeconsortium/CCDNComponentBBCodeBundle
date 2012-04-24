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
class BBCodeEngine extends ContainerAware
{
	
	
	/**
	 *
	 * @access private
	 */
	private $parser_state_flags = array(
		'use_pre_tag' => false,
		'use_pre_tag_child' => null,
		'use_nested' => true,
		'use_nested_child' => null,
	);
	
	
	/**
	 * 
	 * @access private
	 */
	private $lexemes;
	
	
	/**
	 *
	 * @access private
	 * @param $container
	 */
	public function __construct($container)
	{
		$this->container = $container;

		$this->lexemes = $this->container->get('ccdn_component_bb_code.lexeme_table')->getLexemes();
		
	}


	
	/**
	 *
	 * @access public
	 * @return $lexemes[]
	 */
	public function &get_lexemes()
	{
		
		return $this->lexemes;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $input
	 * @return $chunks[]
	 */
	public function bb_scanner($input)
	{
		$chunks = array();
		$regex = '/(\[)|(\])/';
		$symbols = preg_split($regex, $input, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$tag_open = false;
		$current_symbol = '';
		$str = '';
		
		foreach ($symbols as $symbol_key => $symbol)
		{
			$current_symbol = $symbol;
			
			if ($tag_open)
			{
				if ($current_symbol == ']')		// end tag
				{
					$str.= $current_symbol;
					$chunks[] = $str;
					$str = '';
					$tag_open = false;
				} else {				// tag name
					// check for malformed tags
					if ($current_symbol == '[')
					{
						$chunks[] = $str;
						$str = $current_symbol;
					} else {
						$str.= $current_symbol;
					}
				}
			} else {
				if ($current_symbol == '[')		// open tag
				{
					$str = $current_symbol;
					$tag_open = true;
				} else {				// non-tag
					$chunks[] = $current_symbol;
				}
			}
		}
		
		// append any unfinished malformed tags
		if ($str)
		{
			$chunks[] = $str;
		}
			
		return $chunks;
	}


	/**
	 *
	 * @access private
	 * @param $lexemes, $lookup
	 * @return $lookup
	 */
	private function bb_lexeme_lookup(&$lexemes, $lookup)
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
	private function &bb_lexeme_tree_branch(&$tree, $depth)
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
			return $this->bb_lexeme_tree_branch($tree[$tree_size] , --$depth);
		}
	}


	/**
	 *
	 * @access private
	 * @param $branch, $lookup
	 * @return $leaf_key | null
	 */
	private function bb_lexer_find_my_parent(&$branch, $lookup)
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
	public function bb_lexer(&$scan_tree, &$lexemes)
	{
		$lexeme_tree = array();
		$lexeme_tree_depth = 0;

		$scan_tree_size = count($scan_tree);

		for ($scan_key = 0; $scan_key < $scan_tree_size; $scan_key++)
		{
			$lookup = $this->bb_lexeme_lookup($lexemes, $scan_tree[$scan_key]);
			
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
						$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
						
						// find the parent tag
						$parent_leaf_key = $this->bb_lexer_find_my_parent($branch, $lookup);
						
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
						
						$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);

						$branch[] = $lookup;
						
						continue;
					} // end check for determining open or closing tags
				// *******************************************************
				//		SINGULAR TAGS
				// *******************************************************	
				} else {
					// token is a lonewolf type, not matchable!
					$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
					$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
					$lookup['validation_token'] = $token;
					$branch[] = $lookup;
					
					continue;
				} // end token matchable type check
			}

			// correct root tree depth if drops below zero
			if ($lexeme_tree_depth < 0)	{ $lexeme_tree_depth = 0; }

			$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);

			$branch[] = $scan_tree[$scan_key];
		}
		
		return $lexeme_tree;
	}
	
	
	/**
	 *
	 * @access private
	 * @param $lookupStr
	 * @return string|null
	 */
	private function bb_parser_fetch_param_for_tag($lookup_str, $original_lexeme)
	{
		$count = strlen($original_lexeme['symbol_lexeme']);
		
		// /(\[)|(\=)|(\])/
		$regex = '/(\[([a-zA-Z0-9]{0,' . $count . '})\=)|(\])/';
		
		$param = preg_split($regex, $lookup_str, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

		if (is_array($param) && count($param) > 2)
		{
			$len = strlen($param[0]);
			
			if (substr($param[0], $len - 1, $len)  == '=')
			{
				if (array_key_exists('param_is_url', $original_lexeme))
				{
					if ($original_lexeme['param_is_url'] == false)
					{
						// just a regular value
						return $param[2];					
					}
					
					$protocol = preg_split('/(http|https|ftp)/', $param[2], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
					
					if ($protocol[0] == "http" || $protocol[0] == "https" || $protocol[0] == "ftp")
					{
						return $param[2];
					} else {
						return 'http://' . $param[2];
					}
				} else {
					// just a regular value
					return $param[2];					
				}
			}
		}
		
		return null;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $lexemeTree, $lexemes
	 * @return string $html
	 */
	public function bb_parser(&$lexeme_tree, &$lexemes)
	{
		$html = '';

		$use_pre_tag =& $this->parser_state_flags['use_pre_tag'];
		$use_pre_tag_child =& $this->parser_state_flags['use_pre_tag_child'];
		$use_nested =& $this->parser_state_flags['use_nested'];
		$use_nested_child =& $this->parser_state_flags['use_nested_child'];
		$last_tag_content = "";
		
		for ($lexeme_leaf_key = 0; $lexeme_leaf_key < count($lexeme_tree); $lexeme_leaf_key++)
		{
			$lexeme_leaf =& $lexeme_tree[$lexeme_leaf_key];
			
			if (is_array($lexeme_leaf))
			{
				if (array_key_exists('lexeme_key', $lexeme_leaf))
				{
					// we have to check the validation token separately from the lexeme key,
					// because if we only check the validation key, then indices containing
					// lexemes that are invalid will be processed as nested branches, this 
					// will throw various errors, such as invalid array indice offsets etc.
					if (array_key_exists('validation_token', $lexeme_leaf))
					{
						$tag = $lexeme_leaf['symbol_html'];

						// substitute any params
						$tag_param = $this->bb_parser_fetch_param_for_tag($lexeme_leaf['lookup_str'], $lexeme_leaf['original_lexeme']);
						
						if ($tag_param !== null)
						{
							// this section is for tags with multiple choices for a param,
							// in such cases the param provided must match one from the 
							// list provided for that bb tag type.
							if (array_key_exists('param_choices', $lexeme_leaf['original_lexeme']))
							{
								foreach($lexeme_leaf['original_lexeme']['param_choices'] as $param_choice_key => $param_choice)
								{
									if ($tag_param == $param_choice_key)
									{
										$lexeme_leaf['tag_param'] = $param_choice;
										$tag = str_replace('{{param}}', $param_choice, $tag);
										
										break;
									}
								}
							} else {
								$lexeme_leaf['tag_param'] = $tag_param;
								$tag = str_replace('{{param}}', htmlentities($tag_param, ENT_QUOTES/* | ENT_SUBSTITUTE*/), $tag);
							}
						}
						
						// here we are only concerned with the opening tag, and
						// wether it contains a parameter in the opening tag.
						if ($lexeme_leaf['token_key'] == 0)
						{
							if ($use_nested == true)
							{
								if (array_key_exists('use_pre_tag', $lexeme_leaf['original_lexeme']))
								{
									if ($lexeme_leaf['original_lexeme']['use_pre_tag'] == true)
									{
										if ($use_pre_tag == false)
										{
											$use_pre_tag = true;
											$use_pre_tag_child = $lexeme_leaf['ref_child'];
										}
									}
								}
								if (array_key_exists('use_nested', $lexeme_leaf['original_lexeme']))
								{
									if ($lexeme_leaf['original_lexeme']['use_nested'] == false)
									{
										if ($use_nested == true)
										{
											$use_nested = false;
											$use_nested_child = $lexeme_leaf['ref_child'];
										}
									}
								}
							} else {
								$tag = $lexeme_leaf['lookup_str'];
							}
						} else {
							// closing tag stuff
							
							// remove any special state flags for closing tags that match prior opened ones.
							if ($use_pre_tag_child['validation_token'] == $lexeme_leaf['validation_token'])
							{
								$use_pre_tag = false;
								$use_pre_tag_child = null;
							}
							
							if ($use_nested_child['validation_token'] == $lexeme_leaf['validation_token'])
							{
								$use_nested = true;
								$use_nested_child = null;
							}
							
							if ($use_nested == true)
							{
								// if this closing tag has a married opening tag reference,
								// then check if a param exists further in the html counter part.
								if (array_key_exists('ref_parent', $lexeme_leaf))
								{
									if (array_key_exists('tag_param', $lexeme_leaf['ref_parent']))
									{
										$tag = str_replace('{{param}}', htmlentities($lexeme_leaf['ref_parent']['tag_param'], ENT_QUOTES/* | ENT_SUBSTITUTE*/), $tag);
									} else {
										// if {{param}} is in closing half of html and also
										// if left blank, then it should be replaced by the
										// content of the tag instead.
										$tag = str_replace('{{param}}', $last_tag_content, $tag);
									}
								}
							} else {
								$tag = $lexeme_leaf['lookup_str'];
							}
						}
					} else {
						$tag = $lexeme_leaf['lookup_str'];
					}
				
					$html .= $tag;
				
					continue;
				} else {
					if (count($lexeme_tree[$lexeme_leaf_key]) > 0)
					{
						$html .= $this->bb_parser($lexeme_tree[$lexeme_leaf_key] , $lexemes);
						
						continue;
					}
				}
			} else {
				// non tag related, content only just plain
				// old text or garbled invalid bb code tags.
				$tag = $lexeme_leaf;
			}
			
			if ($use_pre_tag == true)
			{
				$str = htmlentities($tag, ENT_QUOTES/* | ENT_SUBSTITUTE*/);
			} else {
				$str = nl2br(htmlentities($tag, ENT_QUOTES/* | ENT_SUBSTITUTE*/));
			}
			
			$last_tag_content = $str;
			
			$html .= $str;
			
		}

		return $html;
	}
	
}