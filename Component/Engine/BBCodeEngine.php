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
 * @category CCDNComponent
 * @package  BBCodeBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNComponentBBCodeBundle
 *
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
	protected $scanner;
		
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
	 * @param LexemeTable $lexemeTable
	 * @param Scanner $scanner
	 * @param Lexer $lexer
	 * @param Parser $parser
	 */
	public function __construct($lexemeTable, $scanner, $lexer, $parser)
	{	
		$this->lexemeTable = $lexemeTable;
		
		$lexer::setLexemeTable($this->lexemeTable);
		$this->scanner = $scanner;
		
		$lexer::setLexemeTable($this->lexemeTable);
		$this->lexer = $lexer;
		
		$parser::setLexemeTable($this->lexemeTable);
		$this->parser = $parser;
	}	
	
	/**
	 *
	 * @access public
	 * @return string $html
	 */
	public function process($input)
	{
		// Warm up the lexeme table.
		$this->lexemeTable->setup();
		
		// Split input string by likely tag format.
		$scanChunks = $this->scanner->process($input);
		
		// Create a symbol tree via the lexer.
		$symbolTree = $this->lexer->process($scanChunks);
		
		// Parse the lexed symbol tree to get an HTML output.
		$html = $this->parser->process($symbolTree);

		return $html;
	}
}