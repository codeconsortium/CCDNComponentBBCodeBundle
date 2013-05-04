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

namespace CCDNComponent\BBCodeBundle\Component\Node\Lexeme;

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
interface LexemeInterface
{
	/**
	 *
	 * @access public
	 * @return string
	 */
	public static function getCanonicalLexemeName();
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public static function getCanonicalGroupName();
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public static function getCanonicalTokenName();
	
	/**
	 * 
	 * @access public
	 * @return int
	 */
	public static function getTokenCount();
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public static function getScanPattern();
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public static function getLexingPattern();
	
//	/**
//	 *
//	 * @access public
//	 * @return bool
//	 */
//	public function isParameterAccepted();
//	
//	/**
//	 *
//	 * @access public
//	 * @return bool
//	 */
//	public function isParameterRequired();
//	
//	/**
//	 *
//	 * @access public
//	 * @return bool
//	 */
//	public function isParameterValid();
//	
//	public function cascadeRender();
	
	/**
	 * 
	 * @access public
	 * @param \CCDNComponent\BBCodeBundle\Component\Lexemes\LexemeInterface
	 * @return bool
	 */
	public static function childAllowed(LexemeInterface $lexeme);
	
	/**
	 *
	 * @access public
	 */
	public static function compileAllowedSubNodeList();
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeGroupWhiteList();
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeGroupBlackList();
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeWhiteList();
	
	/**
	 *
	 * @access public
	 * @return array
	 */
	public static function subNodeBlackList();
}