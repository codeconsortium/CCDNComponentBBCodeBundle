<?php
/*
 * Created by Reece Fowell <me at reecefowell dot com> / <reece at codeconsortium dot com>
 * 17/12/2011
 *
*/

namespace CodeConsortium\BBCodeBundle\Engine;

use Symfony\Component\DependencyInjection\ContainerAware;

class BBCodeEngine extends ContainerAware
{
	
	private $parser_state_flags = array(
/*		'use_pre_tag' => array('state' => false, 'token_holder' => '', ),
		'enable_nested' => array('state' => true, 'token_holder' => '', ),*/
	);

	private $lexemes;
	
	public function __construct()
	{
		$this->lexemes = array(
			array(	'symbol_lexeme' => 'quote',
					'symbol_token' => array('/(\[quote?(=[a-zA-Z0-9]*)*\])/', '/(\[\/quote\])/'),
					'symbol_html' => array('<div class="bb_quote"><b>{{param}}</b><br /><pre>', '</pre></div>'),
					'use_pre_tag' => true,
			),
			array(	'symbol_lexeme' => 'code',
					'symbol_token' => array('/(\[code?(=[a-zA-Z0-9]*)*\])/', '/(\[\/code\])/'),
					'symbol_html' => array('<div class="bb_code">{{param}}<br /><pre class="bb_code">', '</pre></div>'),
					'use_pre_tag' => true,
					'use_nested' => false,
			),	
			array(	'symbol_lexeme' => 'bold',
					'symbol_token' => array('/(\[b\])/', '/(\[\/b\])/'),
					'symbol_html' => array('<b>', '</b>'),
			),
			array(	'symbol_lexeme' => 'underline',
					'symbol_token' => array('/(\[u\])/', '/(\[\/u\])/'),
					'symbol_html' => array('<u>', '</u>'),
			),
			array(	'symbol_lexeme' => 'italics',
					'symbol_token' => array('/(\[i\])/', '/(\[\/i\])/'),
					'symbol_html' => array('<i>', '</i>'),
			),
			array(	'symbol_lexeme' => 'style',
					'symbol_token' => array('/(\[style?(=[a-zA-Z0-9]*)*\])/', '/(\[\/style\])/'),
					'symbol_html' => array('<span class="{{style}}">', '</span>'),
			),
			array(	'symbol_lexeme' => 'subscript',
					'symbol_token' => array('/(\[sub\])/', '/(\[\/sub\])/'),
					'symbol_html' => array('<sub>', '</sub>'),
			),
			array(	'symbol_lexeme' => 'superscript',
					'symbol_token' => array('/(\[sup\])/', '/(\[\/sup\])/'),
					'symbol_html' => array('<sup>', '</sup>'),
			),
			array(	'symbol_lexeme' => 'strikethrough',
					'symbol_token' => array('/(\[strike\])/', '/(\[\/strike\])/'),
					'symbol_html' => array('<del>', '</del>'),
			),
/*			array(	'symbol_lexeme' => 'url',
					'symbol_token' => array('/(\[url=\"\"\])/', '/(\[\/url\])/'),
					'symbol_html' => array('<a href="{{url}}">', '</a>'),
			),
			array(	'symbol_lexeme' => 'image',
					'symbol_token' => array('/(\[img\])/', '/(\[\/img\])/'),
					'symbol_html' => array('<img src="', '" />'),
			),
			array(	'symbol_lexeme' => 'image_named',
					'symbol_token' => array('/(\[img?(=[a-zA-Z0-9]*)*\])/', '/(\[\/img\])/'),
					'symbol_html' => array('<img alt="{{alt}}" src="', '" />'),
			),*/
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
	public function bb_lexeme_lookup(&$lexemes, $lookup)
	{
		foreach ($lexemes as $lexeme_key => $lexeme)
		{
			foreach ($lexeme['symbol_token'] as $token_key => $token)
			{
				if (preg_match($token, $lookup))
				{
					return array('lookup_str' => $lookup, 'lexeme_key' => $lexeme_key, 'token_key' => $token_key, 'original_lexeme' => &$lexeme);
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
				if (count($lexemes[$lookup['lexeme_key']]['symbol_token']) > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
						
						if (is_array($branch[0]))
						{
							if (preg_match($lexemes[$lookup['lexeme_key']]['symbol_token'][0], $branch[0]['lookup_str']))
							{
								// interconnect associative lexeme
								$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
								$branch[0]['validation_token'] = $token;
								$lookup['validation_token'] = $token;

								$branch[] = $lookup;
								
								// nested tags get put in nested arrays.
								$lexeme_tree_depth--;
								
								continue;
							} else {
								// second chance / last ditch effort to align stuff up
								// by getting the parent branch to look for a match
								$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, ($lexeme_tree_depth - 1));
								
								if (is_array($branch[0]))
								{
									if (preg_match($lexemes[$lookup['lexeme_key']]['symbol_token'][0], $branch[0]['lookup_str']))
									{
										// interconnect associative lexeme
										$token = '__' . md5(uniqid(mt_rand(), true)) . '__';
										$branch[0]['validation_token'] = $token;
										$lookup['validation_token'] = $token;

										$branch[] = $lookup;
									
										// nested tags get put in nested arrays.
										$lexeme_tree_depth--;

										continue;
									}
								}
							}
						}
					// *******************************************************
					// 		OPENING TAG
					// *******************************************************
					} else {
						// we go to next nested level
						// deeper down the rabbit hole.
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
	 *
	 */
	public function bb_parser(&$lexeme_tree, &$lexemes)
	{
		$html = '';
		
		for ($lexeme_leaf_key = 0; $lexeme_leaf_key < count($lexeme_tree); $lexeme_leaf_key++)
		{
			$lexeme_leaf = $lexeme_tree[$lexeme_leaf_key];
			$use_pre_tag = false;
			$use_nested = true;
			
			// check for any state flags that could affect this iteration.
			foreach($this->parser_state_flags as $key => &$flag)
			{
				if (array_key_exists('use_pre_tag', $flag))
				{
					$use_pre_tag = true;
				}
				if (array_key_exists('use_nested', $flag))
				{
					$use_nested = false;
				}
			}
						
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
						$tag = $lexemes[$lexeme_leaf['lexeme_key']]['symbol_html'][$lexeme_leaf['token_key']];
				
						// here we are only concerned with the opening tag, and
						// wether it contains a parameter in the opening tag.
						if ($lexeme_leaf['token_key'] == 0)
						{
							$param = preg_split('/(\[)|(\=)|(\])/', $lexeme_leaf['lookup_str'], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
					
							if (is_array($param) && count($param) > 0)
							{
								if ($param[2] == '=')
								{
									$tag = str_replace('{{param}}', $param[3], $tag);
								}
							}
						
							if (array_key_exists('use_pre_tag', $lexeme_leaf['original_lexeme']))
							{
								if ($lexeme_leaf['original_lexeme']['use_pre_tag'] == true)
								{
									$this->parser_state_flags[] = array('use_pre_tag' => true, 'token_holder' => $lexeme_leaf['validation_token']);
								}
							}
							if (array_key_exists('use_nested', $lexeme_leaf['original_lexeme']))
							{
								if ($lexeme_leaf['original_lexeme']['use_nested'] == false)
								{
									$this->parser_state_flags[] = array('use_nested' => false, 'token_holder' => $lexeme_leaf['validation_token']);
								}
							}
						} else {
							// remove any special state flags for closing tags that match prior opened ones.
							if (count($this->parser_state_flags) > 0)
							{
								//for ($flag_index = 0; $flag_index < count($this->parser_state_flags); $flag_index++)
								foreach($this->parser_state_flags as $flag_index => $flag)
								{
									if ($flag['token_holder'] == $lexeme_leaf['validation_token'])
									{
										unset($this->parser_state_flags[$flag_index]);
									}
								}
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
			
			$html .= $str;			
			
		}

		return $html;
	}
	
}