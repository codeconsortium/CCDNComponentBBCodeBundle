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
	protected $lexemeTable;
	
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
	 */
	protected $lexemes;	
	
	/**
	 *
	 * @access private
	 * @param $lexemeTable, $lexer, $parser
	 */
	public function __construct($lexemeTable, $lexer, $parser)
	{	
		$this->lexemeTable = $lexemeTable;
		
		$this->lexer = $lexer;
		$this->lexer->setLexemeTable($this->lexemeTable);
		
		$this->parser = $parser;
		$this->parser->setLexemeTable($this->lexemeTable);
	}	
	
	/**
	 *
	 * @access public
	 * @return string $html
	 */
	public function process($input)
	{
		// Scan the input and break it down into possible tags and body text.
		$regex = '/(\[(?:\/|:)?[A-Z]{1,10}(?:="[ _,.?!@#$%&*()^=\+\-\'\/\w]*"){0,500}?:?\])/';
		
		$scanTree = preg_split($regex, $input, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

		// Create a symbol tree via the lexer.
		$symbolTree = $this->lexer->process($scanTree);
						  
		// Parse the lexed symbol tree to get an HTML output.
		$html = $this->parser->parse($symbolTree);

		return $html;
	}
	
}