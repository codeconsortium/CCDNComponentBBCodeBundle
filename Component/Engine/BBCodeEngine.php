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
	protected $lexemes;

	
	
	/**
	 *
	 * @access protected
	 */
	protected $lexer;
	
	
	
	/**
	 *
	 * @access protected
	 */
	protected $parser;
	
	
	
	/**
	 *
	 * @access private
	 * @param $container
	 */
	public function __construct($container)
	{
		$this->container = $container;

		$this->lexemes = $this->container->get('ccdn_component_bb_code.lexeme_table')->getLexemes();
		
		$this->lexer = $this->container->get('ccdn_component_bb_code.lexer');
		$this->parser = $this->container->get('ccdn_component_bb_code.parser');
	}


	
	/**
	 *
	 * @access public
	 * @return String $html
	 */
	public function process($input)
	{
		$scan_tree 		= &$this->bb_scanner($input);
		$symbol_tree 	= &$this->lexer->process(&$scan_tree, $this->lexemes);
						  $this->lexer->post_process(&$symbol_tree, $this->lexemes);
						
//		echo '<pre>' . print_r(&$symbol_tree, true) . '</pre>'; die();
		$html 			= $this->parser->parse(&$symbol_tree, $this->lexemes);

		return $html;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $input
	 * @return $chunks[]
	 */
	public function &bb_scanner($input)
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
	
}