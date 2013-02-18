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
	 * @access protected
	 */
	protected $parserStateFlags = array(
		'use_pre_tag' => false,
		'use_pre_tag_child' => null,
		'use_nested' => true,
		'use_nested_child' => null,
	);
	
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
	 * @access protected
	 * @param array $symbolTree, array $symbol, string $tag
	 */
	protected function putParamInContext(&$symbolTree, &$symbol, &$tag)
	{					
		$param = null;
		
		if (array_key_exists('ref_child', $symbol))
		{
			if (array_key_exists('tag_param', $symbolTree[$symbol['ref_child']]))
			{
				$param = $symbolTree[$symbol['ref_child']]['tag_param'];
			}
		}
		
		if (array_key_exists('ref_parent', $symbol))
		{			
			if (array_key_exists('tag_param', $symbolTree[$symbol['ref_parent']]))
			{
				$param = $symbolTree[$symbol['ref_parent']]['tag_param'];				
			}
		}
		
		// Any param in current context takes priority and overrides previous param.
		if (array_key_exists('tag_param', $symbol))
		{
			$param = $symbol['tag_param'];
		}
		
		if ($param)		
		{
			$tag = str_replace('{{param}}', htmlentities($param, ENT_QUOTES), $tag);
		} else {
			$tag = str_replace('{{param}}', '', $tag);
		}
	}
	
	/**
	 *
	 * @access public
	 * @param $symbolTree
	 * @return string $html
	 */
	public function parse(&$symbolTree)
	{
		$html = '';
		$lastTagContent = '';
		$symbolTreeSize = count($symbolTree);
		
		$usePreTag =& $this->parserStateFlags['use_pre_tag'];					// This tags html wraps its content in a <pre> tag, so we don't convert \n to <br> as a result.
		$usePreTagChild =& $this->parserStateFlags['use_pre_tag_child'];		// reference to the tag that initiated this <pre> tag state.

		
		for ($symbolTreeKey = 0; $symbolTreeKey < $symbolTreeSize; $symbolTreeKey++)
		{
			$symbol =& $symbolTree[$symbolTreeKey];

			if (is_array($symbol))
			{
				if (array_key_exists('lexeme_key', $symbol))
				{
					$lexeme = $this->lexemeTable->getLexeme($symbol['lexeme_key']);
					
					if (array_key_exists('validation_token', $symbol))
					{
						$tag = $lexeme['symbol_html'][$symbol['token_key']];
						
						// here we are only concerned with the opening tag, and
						// wether it contains a parameter in the opening tag.
						if ($symbol['token_key'] == 0)
						{
							if (array_key_exists('use_pre_tag', $lexeme))
							{
								if ($lexeme['use_pre_tag'] == true)
								{
									if ($usePreTag == false)
									{
										$usePreTag = true;
										$usePreTagChild = $symbolTree[$symbol['ref_child']];
									}
								}
							}
						} else {
							//
							// closing tag stuff
							//
							
							// remove any special state flags for closing tags that match prior opened ones.
							if ($usePreTagChild['validation_token'] == $symbol['validation_token'])
							{
								$usePreTag = false;
								$usePreTagChild = null;
							}
						}

						$this->putParamInContext($symbolTree, $symbol, $tag);
						
					} else {
						// tag has no validation key, so change it from the html token to the lookup str.
						$tag = $symbol['lookup_str'];
					}
				
					$html .= $tag;
				
					continue;
				} else {
					if (count($symbol) > 0)
					{
						$html .= $this->parse($symbol);
						
						continue;
					}
				}
			} else {
				// non tag related, content only just plain
				// old text or garbled invalid bb code tags.
				$tag = $symbol;
			}
						
			if ($usePreTag == true)
			{
				$html .= htmlentities($tag, ENT_QUOTES);
			} else {
				$html .= '<span>' . nl2br(htmlentities($tag, ENT_QUOTES)) . '</span>';
			}
		}

		return $html;
	}

}
