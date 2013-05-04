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

use CCDNComponent\BBCodeBundle\Component\Node\NodeInterface;
use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeInterface;
use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeBaseStatic;

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
 * @abstract
 *
 */
abstract class LexemeBase extends LexemeBaseStatic implements LexemeInterface, NodeInterface
{
	protected $id = null;
	protected $lexingMatch = '';
	protected $tokenIndex = 0;
	protected $matchingNode = null;
	protected $validators = array(
		'param' => false,
		'pairing' => false,
		'nestable' => false,
	);
	
	protected $parameters = array();
	
	public function __construct($lexingMatch)
	{
		$this->lexingMatch = $lexingMatch;
	
		$canonicalLookupStr = strtoupper($lexingMatch);
	
		foreach (static::$lexingPattern as $index => $pattern) {
			if (preg_match($pattern, $canonicalLookupStr)) {
			
				$this->tokenIndex = $index;
			
				break;
			}
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function isOpeningTag()
	{
		return ($this->tokenIndex < 1 ? true : false);
	}
	
	public function isClosingTag()
	{
		return ($this->tokenIndex > 0 ? true : false);
	}

	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getLexingMatch()
	{
		return $this->lexingMatch;
	}
	
	
	public function setMatchingNode(LexemeInterface $node, $id)
	{
		$this->matchingNode = $node;
		
		$this->id = $id;
	}
	
	public function hasMatchingNode()
	{
		$match = $this->matchingNode;
		
		if (null == $match) {
			return false;
		}
		
		if (static::getCanonicalLexemeName() == $match::getCanonicalLexemeName()) {
			return true;
		}
		
		return false;
	}
	
	public function getMatchingNode()
	{
		return $this->matchingNode;
	}
	
	public function isValid($checkMatching = false)
	{
		if (! $this->validators['param']) {
			return false;
		}
		
		if (! static::isStandalone()) {
			if (! $this->validators['pairing']) {
				return false;
			} else {
				if ($checkMatching) {
					if (! $this->matchingNode->isValid(false)) {
						return false;
					}				
				}
			}			
		}
		
		if (! $this->validators['nestable']) {
			return false;
		}

		return true;
	}
	

	
	public function areAllParametersValid()
	{
		return true;
	}
	
	public function extractParameters()
	{
		
	}
	
	public function cascadeValidate(NodeInterface $lastValid = null)
	{
		$this->extractParameters();
		
		if ($this->areAllParametersValid()) {
			$this->passValidationForParam();
		}
				
		$this->parentValid = $lastValid;
		
		if (null !== $lastValid) {
			if ($lastValid::childAllowed($this)) {
				$this->passValidationForNestable();
			}
		} else {
			$this->passValidationForNestable();
		}
		
		if (static::isStandalone()) {
			$this->passValidationForPairing();
		} else {
			if ($this->hasMatchingNode()) {
				$this->passValidationForPairing();
			}			
		}
	}
	
	public function passValidationForParam()
	{
		$this->validators['param'] = true;
	}
	
	public function getValidationIsParamPassing()
	{
		return $this->validators['param'];
	}
	
	public function passValidationForPairing()
	{
		$this->validators['pairing'] = true;
	}
	
	public function getValidationIsPairingPassing()
	{
		return $this->validators['pairing'];
	}
	
	public function passValidationForNestable()
	{
		$this->validators['nestable'] = true;
	}
	
	public function getValidationIsNestablePassing()
	{
		return $this->validators['nestable'];
	}
	
	public function dump()
	{
		$out = '<br><ul>';
		$out .=	'<li><b>' . static::$canonicalLexemeName . ':</b> "' . $this->lexingMatch . '"</li>';
		$out .= '<li><b>id:</b> ' . $this->getId() . '</li>';
		
		$out .= '<li><b>param:</b> <font style="color:red">' . $this->validators['param'] . '</font></li>';
		$out .= '<li><b>pairing:</b> <font style="color:red">' . $this->validators['pairing'] . '</font></li>';
		$out .= '<li><b>nestable:</b> <font style="color:red">' . $this->validators['nestable'] . '</font></li>';
		$out .= '<li><b>total:</b> <font style="color:red">' . $this->isValid(true) . '</font></li>';
		
		return $out . '</ul>';
	}
	
	public function getErrors()
	{
		$errors = array();
			
		if (! $this->getValidationIsNestablePassing()) {
			$errors[] = 'This tag is not allowed here.';
		} else {
			if (! $this->getValidationIsParamPassing()) {
				$errors[] = 'Parameter(s) attributes invalid.';
			}
		
			if (! static::isStandalone()) {
				if (! $this->getValidationIsPairingPassing()) {
					$errors[] = 'Could not match the other tag.';
				} else {
					if (! $this->matchingNode->isValid()) {
						$errors[] = 'Problem with matching tag.';
					}
				}
			}	
		}
				
		return $errors;
	}
	
	public function renderErrors()
	{
		$errors = $this->getErrors();

		$message = '';
		
		foreach ($errors as $error) {
			$message .= '<li>' . $error . '</li>';
		}
		
		$message = '<ul>' . $message . '</ul>';
		
		//return '<span class="bb_invalid_tag hint--bottom hint--error hint--rounded" data-hint="' . $message . '">' . htmlentities($this->lexingMatch, ENT_QUOTES) . '</span>';
		return '<span class="bb_invalid_tag" data-tip="bottom" title data-original-title="' . $message . '">' . htmlentities($this->lexingMatch, ENT_QUOTES) . '</span>';
	}
	
	/**
	 * 
	 * @access public
	 */
	public function cascadeRender()
	{	
		if ($this->isValid(true)) {
			return static::$lexingHtml[$this->tokenIndex];
		}
	
		return $this->renderErrors();
	}
}