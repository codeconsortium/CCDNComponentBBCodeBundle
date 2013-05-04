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

namespace CCDNComponent\BBCodeBundle\Component\Node\Lexeme\Tag\Format;

use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeBase;
use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeInterface;

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
class Heading3 extends LexemeBase implements LexemeInterface
{
	/**
	 * 
	 * @var string $canonicalLexemeName
	 */
	protected static $canonicalLexemeName = 'Heading3';
	
	/**
	 * 
	 * @var string $canonicalTokenName
	 */
	protected static $canonicalTokenName = 'H3';
	
	/**
	 * 
	 * @var string $canonicalGroupName
	 */
	protected static $canonicalGroupName = 'Format';	
	
	/**
	 * 
	 * @var bool $isParameterAccepted
	 */
	protected static $parametersAcceptedOnToken = array();
			
	/**
	 * 
	 * @var bool $isParameterRequired
	 */
	protected static $parametersRequiredOnToken = array();
	
	protected static $isStandalone = false;	
	
	/**
	 * 
	 * @var array $lexingPattern
	 */
	protected static $lexingPattern = array('/^\[H3\]$/', '/^\[\/H3\]$/');
	protected static $lexingHtml = array('<h3>', '</h3>');
	
	/**
	 * 
	 * @var array $allowedNestable
	 */
	protected static $allowedNestable = array();
	protected static $lexemeTable = array();

	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeGroupWhiteList()
	{
		return array(
			'*',
		);
	}
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeGroupBlackList()
	{
		return array(
			'Asset',
			'Block',
		);
	}
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeWhiteList()
	{
		return array(
			'*',
		);
	}
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeBlackList()
	{
		return array(
			
		);
	}
}