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

use CCDNComponent\BBCodeBundle\Component\Node\Lexeme\LexemeInterface;
use CCDNComponent\BBCodeBundle\Component\Node\NodeInterface;
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
	 * @param string $lexingMatch
	 */
	public function __construct($lexingMatch);
	
	/**
	 * 
	 * @access public
	 * @return int
	 */
	public function getId();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function isOpeningTag();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function isClosingTag();

	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getLexingMatch();
	
	/**
	 * 
	 * Sets the matching node paired with this one.
	 * 
	 * @access public
	 * @param NodeInterface $node
	 * @param string $id
	 */
	public function setMatchingNode(LexemeInterface $node, $id);
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function hasMatchingNode();
	
	/**
	 * 
	 * Returns the matching node paired with this one.
	 * 
	 * @access public
	 * @return NodeInterface
	 */
	public function getMatchingNode();
	
	/**
	 * 
	 * Use the param $checkMatching to further cascade the
	 * check to the partner node linked in via $this->matchingNode.
	 * 
	 * This cascading requires the check to prevent an infinite recursion.
	 * 
	 * @access public
	 * @param bool $checkMatching
	 * @return bool
	 */
	public function isValid($checkMatching = false);

	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function areAllParametersValid();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function extractParameters();
	/**
	 * 
	 * Runs all necessary checks to determine if this
	 * lexeme is valid and checks them off as it goes.
	 * 
	 * @access public
	 * @param NodeInterface $lastValid
	 */
	public function cascadeValidate(NodeInterface $lastValid = null);
	
	/**
	 * 
	 * Sets this validation item from the checklist off as passing.
	 * 
	 * @access public
	 */
	public function passValidationForParam();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function getValidationIsParamPassing();
	
	/**
	 * 
	 * Sets this validation item from the checklist off as passing.
	 * 
	 * @access public
	 */
	public function passValidationForPairing();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function getValidationIsPairingPassing();
	
	/**
	 * 
	 * Sets this validation item from the checklist off as passing.
	 * 
	 * @access public
	 */
	public function passValidationForNestable();
	
	/**
	 * 
	 * @access public
	 * @return bool
	 */
	public function getValidationIsNestablePassing();
	
	/**
	 * 
	 * When debugging, call on dump() to view contents of
	 * nodes recursively from the root node of the tree.
	 * 
	 * @access public
	 * @return string
	 */
	public function dump();
	
	/**
	 * 
	 * Will return an array of errors concering why
	 * this lexeme is unable to render itself.
	 * 
	 * @access public
	 * @return string
	 */
	public function getErrors();
	
	/**
	 * 
	 * Renders an array of errors concering why
	 * this lexeme is unable to render itself.
	 * 
	 * @access public
	 * @return string
	 */
	public function renderErrors();
	
	/**
	 * 
	 * Renders the html from the $lexingHtml index matching
	 * this nodes index from the $lexingPatterns index.
	 * 
	 * @access public
	 * @return string
	 */
	public function cascadeRender();
}