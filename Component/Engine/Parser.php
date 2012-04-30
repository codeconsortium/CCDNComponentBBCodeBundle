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
class Parser
{
	
	
	/**
	 *
	 * @access private
	 */
	protected $parser_state_flags = array(
		'use_pre_tag' => false,
		'use_pre_tag_child' => null,
		'use_nested' => true,
		'use_nested_child' => null,
	);
	
	
	/**
	 *
	 * @access private
	 * @param $lookupStr
	 * @return string|null
	 */
	private function fetch_param_for_tag($lookup_str, $original_lexeme)
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
	public function parse(&$lexeme_tree, &$lexemes)
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
						$tag_param = $this->fetch_param_for_tag($lexeme_leaf['lookup_str'], $lexeme_leaf['original_lexeme']);
						
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
						$html .= $this->parse($lexeme_tree[$lexeme_leaf_key] , $lexemes);
						
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
