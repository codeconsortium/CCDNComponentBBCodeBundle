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
 */

namespace CCDNComponent\BBCodeBundle\Component\TwigExtension;

use CCDNComponent\BBCodeBundle\Engine\BBCodeEngine;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class BBCodeExtension extends \Twig_Extension
{
	
	/**
	 *
	 * @access protected
	 */
	protected $engine;
	
	/**
	 *
	 * @access protected
	 */
	protected $enable;	
	
	/**
	 *
	 * @access public
	 * @param $container
	 */
	public function __construct($container)
	{
		$this->container = $container;
		
		$this->engine = $this->container->get('ccdn_component_bb_code.engine');
		$this->enable = $this->container->getParameter('ccdn_component_bb_code.parser.enable');
	}



	/**
	 *
	 * @access public
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'BBCode' => new \Twig_Function_Method($this, 'BBCode'),
			'BBCodeFetchChoices' => new \Twig_Function_Method($this, 'BBCodeFetchChoices'),
			'BBCodeFetchGroup' => new \Twig_Function_Method($this, 'BBCodeFetchGroup'),
			'BBCodeFetchElementIDforTheme' => new \Twig_Function_Method($this, 'BBCodeFetchElementIDforTheme'),
		);
	}
	
	
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return 'BBCode';
	}
	
	
	
	/**
	 *
	 * Converts BBCode to HTML via breaking down the BBCode tags into lexemes (via lexer method), which are 
	 * then stored in the form of an array, this array is then given to the parser which matches the proper 
	 * pairs to their HTML equivalent. Pairs must have a matching token, matched/unmatched are marked as
	 * so during the lexing process via an reference token.
	 *
	 * @access public
	 * @param $input
	 * @return string $html
	 */
	public function BBCode($input, $enableOnDemand)
	{
		if ($this->enable && $enableOnDemand)
		{
			$html = $this->engine->process($input);
		} else {

			$html = nl2br(htmlentities($input, ENT_QUOTES));			
		}
		
		return $html;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $tag
	 * @return array
	 */
	public function BBCodeFetchChoices($tag)
	{
		$lexemes = $this->engine->getLexemes();
		
		foreach($lexemes as $lexeme)
		{
			if ($lexeme['symbol_lexeme'] == $tag)
			{
				if (array_key_exists('param_choices', $lexeme))
				{
					return $lexeme['param_choices'];
				}
				
				return array();
			}
		}
		
		return array();
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $group
	 * @return array
	 */
	public function BBCodeFetchGroup($group)
	{
		$lexemes = $this->engine->getLexemes();
		
		$found = array();
		
		foreach($lexemes as $lexeme_key => $lexeme)
		{
			if (array_key_exists('group', $lexeme))
			{
				if ($lexeme['group'] == $group)
				{
					$found[] = $lexeme;
				}
			}
		}
		
		return $found;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $attributes
	 * @return int $id
	 */
	public function BBCodeFetchElementIDforTheme($attributes)
	{
		preg_match('/id\=\"([a-zA-Z0-9_[]]*)*\"/', $attributes, $matches, 0, 0);
		$id = substr($matches[0], 4, -1);
		return $id;
	}

}