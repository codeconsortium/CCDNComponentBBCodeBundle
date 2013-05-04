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

namespace CCDNComponent\BBCodeBundle\Component\Node\Lexeme\Tag\Asset;

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
class Image extends LexemeBase implements LexemeInterface
{
	/**
	 * 
	 * @var string $canonicalLexemeName
	 */
	protected static $canonicalLexemeName = 'Image';
	
	/**
	 * 
	 * @var string $canonicalTokenName
	 */
	protected static $canonicalTokenName = 'IMG';
	
	/**
	 * 
	 * @var string $canonicalGroupName
	 */
	protected static $canonicalGroupName = 'Asset';	
	
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

	protected static $isStandalone = true;	
	
	/**
	 * 
	 * @var array $lexingPattern
	 */
//	protected static $lexingPattern = array('/^\[IMG?(\=[\P{C}\p{Cc}]*)*\]$/');
	protected static $lexingPattern = array('/^\[IMG?(\=(.*?)*)\]$/');
	
	protected static $lexingHtml = array('<img src="{{ param[0] }}" title="{{ param[1] }}" />');
	
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
			// 3. Store Parameter.
			$this->parameters[0] = $param[2];

			return true;
		}
		
		return false;
	}
	
	public function areAllParametersValid()
	{
		if (array_key_exists(0, $this->parameters)) {
			if ($this->parameters[0] !== null) {
				return true;				
			}
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
			return str_replace('{{ param[0] }}', '<strong>' . $this->parameters[0] . '</strong><hr>', static::$lexingHtml[$this->tokenIndex]);
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