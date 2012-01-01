<?php
/*
 * Created by Reece Fowell 
 * <me at reecefowell dot com> 
 * <reece at codeconsortium dot com>
 * Created on 17/12/2011
 *
*/

namespace CodeConsortium\BBCodeBundle\Engine;

use Symfony\Component\DependencyInjection\ContainerAware;

class BBCodeEngine extends ContainerAware
{
	private $parser_state_flags = array(
		'use_pre_tag' => false,
		'use_pre_tag_child' => null,
		'use_nested' => true,
		'use_nested_child' => null,
	);
	
	private $lexemes;
	
	public function __construct($container)
	{
		$this->container = $container;

		$label_said = $this->container->get('translator')->trans('bb_code.quote.said', array(), 'CodeConsortiumBBCodeBundle');
		$label_code = $this->container->get('translator')->trans('bb_code.code', array(), 'CodeConsortiumBBCodeBundle');
		
		$this->lexemes = array(
			array(	'symbol_lexeme' => 'quote',
					'symbol_token' => array('/(\[QUOTE?(\=[a-zA-Z0-9 ]*)*\])/', '/(\[\/QUOTE\])/'),
					'symbol_html' => array('<div class="bb_quote"><b>{{param}} ' . $label_said . ':</b><br /><pre>', '</pre></div>'),
					'use_pre_tag' => true,
			),
			array(	'symbol_lexeme' => 'code',
					'symbol_token' => array('/(\[CODE?(\=[a-zA-Z0-9 #+.]*)*\])/', '/(\[\/CODE\])/'),
					'symbol_html' => array('<div class="bb_code"><div class="bb_code_strip">' . $label_code . ': {{param}}</div><pre class="bb_code">', '</pre></div>'),
					'use_pre_tag' => true,
					'use_nested' => false,
			),	
			array(	'symbol_lexeme' => 'bold',
					'symbol_token' => array('/(\[B\])/', '/(\[\/B\])/'),
					'symbol_html' => array('<b>', '</b>'),
			),
			array(	'symbol_lexeme' => 'underline',
					'symbol_token' => array('/(\[U\])/', '/(\[\/U\])/'),
					'symbol_html' => array('<u>', '</u>'),
			),
			array(	'symbol_lexeme' => 'italics',
					'symbol_token' => array('/(\[I\])/', '/(\[\/I\])/'),
					'symbol_html' => array('<i>', '</i>'),
			),
			array(	'symbol_lexeme' => 'style',
					'symbol_token' => array('/(\[STYLE?(\=[a-zA-Z0-9 ]*)*\])/', '/(\[\/STYLE\])/'),
					'symbol_html' => array('<span class="{{param}}">', '</span>'),
					'param_choices' => array('title' => 'bb_style_title', 'heading' => 'bb_style_heading', 'sub section' => 'bb_style_sub_section', 'body' => 'bb_style_body'),
			),
			array(	'symbol_lexeme' => 'subscript',
					'symbol_token' => array('/(\[SUB\])/', '/(\[\/SUB\])/'),
					'symbol_html' => array('<sub>', '</sub>'),
			),
			array(	'symbol_lexeme' => 'superscript',
					'symbol_token' => array('/(\[SUP\])/', '/(\[\/SUP\])/'),
					'symbol_html' => array('<sup>', '</sup>'),
			),
			array(	'symbol_lexeme' => 'strikethrough',
					'symbol_token' => array('/(\[STRIKE\])/', '/(\[\/STRIKE\])/'),
					'symbol_html' => array('<del>', '</del>'),
			),
			array(	'symbol_lexeme' => 'url',
					'symbol_token' => array('/(\[URL?(\=[a-zA-Z0-9 ]*)*\])/', '/(\[\/URL\])/'),
					'symbol_html' => array('<a href="', '">{{param}}</a>'),
			),
			array(	'symbol_lexeme' => 'image',
					'symbol_token' => array('/(\[IMG?(\=[a-zA-Z0-9 ]*)*\])/', '/(\[\/IMG\])/'),
					'symbol_html' => array('<img alt="{{param}}" src="', '" />'),
			),
		);
		
		foreach($this->lexemes as $key => &$lexeme)
		{
			$lexeme['token_count'] = count($lexeme['symbol_token']);
		}
	}
	
	public function &get_lexemes()
	{
		return $this->lexemes;
	}
	
	/**
	 *
	 *
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
	 *
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
	 *
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
	 *
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
	
	private function bb_parser_fetch_param_for_tag($lookup_str)
	{
		$param = preg_split('/(\[)|(\=)|(\])/', $lookup_str, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

		if (is_array($param) && count($param) > 0)
		{
			if ($param[2] == '=')
			{
				return $param[3];
			}
		}
		
		return null;
	}
	/**
	 *
	 *
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
						$tag_param = $this->bb_parser_fetch_param_for_tag($lexeme_leaf['lookup_str']);
						
						if ($tag_param !== null)
						{
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
								$tag = str_replace('{{param}}', htmlentities($tag_param, ENT_QUOTES|ENT_SUBSTITUTE), $tag);
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
										$tag = str_replace('{{param}}', htmlentities($lexeme_leaf['ref_parent']['tag_param'], ENT_QUOTES|ENT_SUBSTITUTE), $tag);
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
				$str = htmlentities($tag, ENT_QUOTES|ENT_SUBSTITUTE);
			} else {
				$str = nl2br(htmlentities($tag, ENT_QUOTES|ENT_SUBSTITUTE));
			}
			
			$last_tag_content = $str;
			
			$html .= $str;
			
		}

		return $html;
	}
	
}