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
	 * 1) First level index should match the token
	 *    index that the parameter will be found in.
	 * 2) Second level index should specify the
	 *    order of the parameter.
	 *
	 * @var array $parametersAcceptedOnToken
	 */
	protected static $parametersAcceptedOnToken = array();		
	
	/**
	 *
	 * These parameters will be mandatory. All parameters
	 * specified here must also be reflected in the above
	 * $parametersAcceptedOnToken and the index must match
	 * must match the same index for each parameter in
	 * before mentioned $parametersAcceptedOnToken.
	 * 
	 * 1) First level index should match the token
	 *    index that the parameter will be found in.
	 * 2) Second level index should specify the
	 *    order of the parameter.
	 *
	 * @var array $parametersRequiredOnToken
	 */
	protected static $parametersRequiredOnToken = array();
	
	/**
	 * 
	 * Specify wether this tag is paired with another for 
	 * a successful lexing/validation match to take place.
	 * 
	 * @var bool $isStandalone
	 */
	protected static $isStandalone = true;	
	
	/**
	 * 
	 * @var bool $isLexable
	 */
	protected static $isLexable = false;
	
	/**
	 * 
	 * Regular expressions to match against the
	 * scan chunk during lexing process. The order
	 * must match the $lexingHtml variable.
	 * 
	 * @var array $lexingPattern
	 */
	protected static $lexingPattern = array();
	
	/**
	 * 
	 * Specifies the array of other lexemes that
	 * are permitted to be valid and rendered between
	 * a matching pair of this particular lexeme.
	 * 
	 * @var array $allowedNestable
	 */
	protected static $allowedNestable = array();
	
	/**
	 * 
	 * 
	 * 
	 */
	protected static $lexemeTable = array();
	
	/**
	 * 
	 * Preset validators so all are passing by default.
	 * 
	 * @var $validators
	 */
	protected $validators = array(
		'param' => true,
		'pairing' => true,
		'nestable' => true,
	);
	
	/**
	 * 
	 * Renders the html from the $lexingHtml index matching
	 * this nodes index from the $lexingPatterns index.
	 * 
	 * @access public
	 * @return string
	 */
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
	 * Rigged for the purposes of PlainText not caring about its content.
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