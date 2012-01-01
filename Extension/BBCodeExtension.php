<?php
/*
 * Created by Reece Fowell <me at reecefowell dot com> / <reece at codeconsortium dot com>
 * 17/12/2011
 *
*/

namespace CodeConsortium\BBCodeBundle\Extension;

use CodeConsortium\BBCodeBundle\Engine\BBCodeEngine;

class BBCodeExtension extends \Twig_Extension
{
	public function __construct($container)
	{
		$this->container = $container;
	}

	public function getFunctions()
	{
		return array(
			'BBCode' => new \Twig_Function_Method($this, 'BBCode'),
			'BBCodeFetchChoices' => new \Twig_Function_Method($this, 'BBCodeFetchChoices'),
			'BBCodeFetchElementIDforTheme' => new \Twig_Function_Method($this, 'BBCodeFetchElementIDforTheme'),
		);
	}
	
	public function getName()
	{
		return 'BBCode';
	}
	
	// Converts BBCode to HTML via breaking down the BBCode tags into lexemes (via lexer method), which are 
	// then stored in the form of an array, this array is then given to the parser which matches the proper 
	// pairs to their HTML equivalent. Pairs must have a matching token, matched/unmatched are marked as
	// so during the lexing process via an reference token.
	public function BBCode($input)
	{
		$engine = $this->container->get('bb_code.engine');
		 
		$scan_tree 		= $engine->bb_scanner($input);
		$lexeme_tree 	= $engine->bb_lexer($scan_tree, $engine->get_lexemes());
		$html 			= $engine->bb_parser($lexeme_tree, $engine->get_lexemes());
		
		return $html;
	}
	
	public function BBCodeFetchChoices($tag)
	{
		$engine = $this->container->get('bb_code.engine');	

		$choices =& $engine->get_lexemes();
		
		foreach($choices as $choice_key => $choice)
		{
			if ($choice['symbol_lexeme'] == $tag)
			{
				if (array_key_exists('param_choices', $choice))
				{
					return $choice['param_choices'];
				}
				
				return array();
			}
		}
		
		return array();
	}
	
	public function BBCodeFetchElementIDforTheme($attributes)
	{
		preg_match('/id\=\"([a-zA-Z0-9_[]]*)*\"/', $attributes, $matches, 0, 0);
		$id = substr($matches[0], 4, -1);
	//	echo '<pre>' . print_r($id, true) . '</pre>'; die();
		return $id;
	}

}