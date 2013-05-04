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

namespace CCDNComponent\BBCodeBundle\Component\Node\Lexeme\Tag;

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
class PlainText extends LexemeBase implements LexemeInterface
{
	/**
	 * 
	 * @var string $canonicalLexemeName
	 */
	protected static $canonicalLexemeName = 'PlainText';
	
	/**
	 * 
	 * @var string $canonicalTokenName
	 */
	protected static $canonicalTokenName = 'PLAINTEXT';
	
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
	
	protected static $isStandalone = true;	
	
	/**
	 * 
	 * @var bool $isLexable
	 */
	protected static $isLexable = false;
	
	/**
	 * 
	 * @var array $lexingPattern
	 */
	protected static $lexingPattern = array();
	
	/**
	 * 
	 * @var array $allowedNestable
	 */
	protected static $allowedNestable = array();
	protected static $lexemeTable = array();
		
	protected $validators = array(
		'param' => true,
		'pairing' => true,
		'nestable' => true,
	);
	
	public function cascadeRender()
	{
		$parent = $this->getNodeParent();
		
		if ($parent) {
			$fn = $parent->getNodeFirst();
			if ($fn) {
				if ($fn::getCanonicalGroupName() != 'Format') {
					return htmlentities(ltrim(rtrim($this->lexingMatch)), ENT_QUOTES).'&shy;';
				}	
			}
		}
		
		// &shy; is an invisible char, without it, PHP ignores newlines for some reason, very unusual behaviour.
		return htmlentities($this->lexingMatch, ENT_QUOTES).'&shy;';
	}

	/**
	 * 
	 * @access public
	 * @param \CCDNComponent\BBCodeBundle\Component\Lexemes\LexemeInterface
	 * @return bool
	 */
	public static function childAllowed(LexemeInterface $lexeme)
	{
		return true;
	}
	
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