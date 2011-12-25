<?php
/*
 * Created by Reece Fowell <me at reecefowell dot com> / <reece at codeconsortium dot com>
 * 17/12/2011
 *
*/

namespace CodeConsortium\BBCodeBundle\Engine;

class BBCodeEngine
{

	private $lexemes;
	
	public function __construct()
	{
		$this->lexemes = array(
			array(	'lexeme' => 'quote',
					'token' => array('/(\[quote?(=[a-zA-Z0-9]*)*\])/', '/(\[\/quote\])/'),
					'html' => array('<div class="bb_quote"><b>{{param}}</b><hr />', '</div>'),
			),
			array(	'lexeme' => 'code',
					'token' => array('/(\[code?(=[a-zA-Z0-9]*)*\])/', '/(\[\/code\])/'),
					'html' => array('<div class="bb_code">{{param}}', '</div>'),
			),	
			array(	'lexeme' => 'bold',
					'token' => array('/(\[b\])/', '/(\[\/b\])/'),
					'html' => array('<b>', '</b>'),
			),
			array(	'lexeme' => 'underline',
					'token' => array('/(\[u\])/', '/(\[\/u\])/'),
					'html' => array('<u>', '</u>'),
			),
			array(	'lexeme' => 'italics',
					'token' => array('/(\[i\])/', '/(\[\/i\])/'),
					'html' => array('<i>', '</i>'),
			),
			array(	'lexeme' => 'style',
					'token' => array('/(\[style?(=[a-zA-Z0-9]*)*\])/', '/(\[\/style\])/'),
					'html' => array('<span class="{{style}}">', '</span>'),
			),
			array(	'lexeme' => 'subscript',
					'token' => array('/(\[sub\])/', '/(\[\/sub\])/'),
					'html' => array('<sub>', '</sub>'),
			),
			array(	'lexeme' => 'superscript',
					'token' => array('/(\[sup\])/', '/(\[\/sup\])/'),
					'html' => array('<sup>', '</sup>'),
			),
			array(	'lexeme' => 'strikethrough',
					'token' => array('/(\[strike\])/', '/(\[\/strike\])/'),
					'html' => array('<del>', '</del>'),
			),
/*			array(	'lexeme' => 'url',
					'token' => array('/(\[url=\"\"\])/', '/(\[\/url\])/'),
					'html' => array('<a href="{{url}}">', '</a>'),
			),
			array(	'lexeme' => 'image',
					'token' => array('/(\[img\])/', '/(\[\/img\])/'),
					'html' => array('<img src="', '" />'),
			),
			array(	'lexeme' => 'image_named',
					'token' => array('/(\[img?(=[a-zA-Z0-9]*)*\])/', '/(\[\/img\])/'),
					'html' => array('<img alt="{{alt}}" src="', '" />'),
			),*/
		);
		
		foreach($this->lexemes as $key => &$lexeme)
		{
			$lexeme['token_count'] = count($lexeme['token']);
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
			foreach ($lexeme['token'] as $token_key => $token)
			{
				if (preg_match($token, $lookup))
				{
					return array('lookup_str' => $lookup, 'lexeme_key' => $lexeme_key, 'token_key' => $token_key);
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
				if (count($lexemes[$lookup['lexeme_key']]['token']) > 1)
				{
					// *******************************************************
					// 		CLOSING TAG
					// *******************************************************
					if ($lookup['token_key'] == 1)
					{
						$branch = &$this->bb_lexeme_tree_branch($lexeme_tree, $lexeme_tree_depth);
						
						if (is_array($branch[0]))
						{
							if (preg_match($lexemes[$lookup['lexeme_key']]['token'][0], $branch[0]['lookup_str']))
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
									if (preg_match($lexemes[$lookup['lexeme_key']]['token'][0], $branch[0]['lookup_str']))
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
						
			if (is_array($lexeme_leaf))
			{
				if (array_key_exists('lexeme_key', $lexeme_leaf))
				{
					if (array_key_exists('validation_token', $lexeme_leaf))
					{
						$tag = $lexemes[$lexeme_leaf['lexeme_key']]['html'][$lexeme_leaf['token_key']];
					
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
						}
					} else {
						$tag = $lexeme_leaf['lookup_str'];
					}
					$html .= $tag;
				} else {
					if (count($lexeme_tree[$lexeme_leaf_key]) > 0)
					{
						$html .= $this->bb_parser($lexeme_tree[$lexeme_leaf_key] , $lexemes);
					}
				}
			} else {
				$html .= nl2br(htmlspecialchars($lexeme_leaf, ENT_QUOTES));
			}
		}

		return $html;
	}
	
}