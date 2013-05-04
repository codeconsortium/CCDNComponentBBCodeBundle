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
class Link extends LexemeBase implements LexemeInterface
{
	/**
	 * 
	 * @var string $canonicalLexemeName
	 */
	protected static $canonicalLexemeName = 'Link';
	
	/**
	 * 
	 * @var string $canonicalTokenName
	 */
	protected static $canonicalTokenName = 'URL';
	
	/**
	 * 
	 * @var string $canonicalGroupName
	 */
	protected static $canonicalGroupName = 'Format';	
	
	/**
	 * 
	 * @var bool $isParameterAccepted
	 */
	protected static $parametersAcceptedOnToken = array(0 => array(0 => 'url'));
	
	/**
	 * 
	 * @var bool $isParameterRequired
	 */
	protected static $parametersRequiredOnToken = array(0 => array(0 => 'url'));

	protected static $isStandalone = false;	
	
	/**
	 * 
	 * @var array $lexingPattern
	 */
	protected static $lexingPattern = array('/^\[URL?(\=(.*?)*)\]$/', '/^\[\/URL\]$/');
	
	protected static $lexingHtml = array('<a href="{{ param[0] }}">', '</a>');
	
	/**
	 * 
	 * @var array $allowedNestable
	 */
	protected static $allowedNestable = array();
	protected static $lexemeTable = array();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function extractParameters()
	{
		// 1. Extract Parameter.
		$symbols = '\d\w _,.?!@#$%&*()^=:\+\-\'\/';
		$regex = '/(\=\"(['.$symbols.']*)\"{0,500})/';

		$param = preg_split($regex, $this->lexingMatch, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		
		// 2. Check Parameter meets some criteria.
		if (is_array($param) && count($param) > 2) {
			$protocol = preg_split('/^(http|https|ftp)\:\/\//', $param[2], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

			// 3. Store Parameter.
			if ($protocol[0] == 'http' || $protocol[0] == 'https' || $protocol[0] == 'ftp') {
				$url = $param[2];
			} else {
				$url = 'http://' . $param[2];
			}

			$this->parameters[0] = $url;
			
			return true;
		}
		
		return false;
	}
	
	public function areAllParametersValid()
	{
		if ($this->tokenIndex == 0) {
			if (array_key_exists(0, $this->parameters)) {
				if ($this->parameters[0] !== null) {
					return true;				
				}
			}
		} else {
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 * @access public
	 */
	public function cascadeRender()
	{
		if ($this->isValid(true)) {
			if ($this->tokenIndex == 0) {
				return str_replace('{{ param[0] }}', $this->parameters[0], static::$lexingHtml[$this->tokenIndex]);
			} else {
				return static::$lexingHtml[$this->tokenIndex];
			}
		}
	
		return $this->renderErrors();
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