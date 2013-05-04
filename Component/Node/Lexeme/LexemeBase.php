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
    /**
     *
     * Usually a randomly generated hash string
     * that both pairs of 2 matching nodes will have.
     *
     * @var string $id
     */
    protected $id = null;

    /**
     *
     * The string for the given lexeme matched against.
     *
     * @var string $lexingMatch
     */
    protected $lexingMatch = '';

    /**
     *
     * The index of the token which we matched from
     * the lexemes regular expression patterns.
     *
     * @var int $tokenIdex
     */
    protected $tokenIndex = 0;

    /**
     *
     * A reference point to another node that has been
     * paired with this one on the same branch of the tree.
     *
     * @var NodeInterface $matchingNode
     */
    protected $matchingNode = null;

    /**
     *
     * A check list will go through setting to true for each
     * item as we do some validation checks. We will check
     * them all later during rendering to decide if to render.
     *
     * @var array $validators
     */
    protected $validators = array(
        'param' => false,
        'pairing' => false,
        'nestable' => false,
    );

    /**
     *
     * A list of parameters that we have successfully extracted.
     *
     * You will have to write your own extractParameters() method for this.
     *
     * @var array $parameters
     */
    protected $parameters = array();

    /**
     *
     * @access public
     * @param string $lexingMatch
     */
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

    /**
     *
     * @access public
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function isOpeningTag()
    {
        return ($this->tokenIndex < 1 ? true : false);
    }

    /**
     *
     * @access public
     * @return bool
     */
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

    /**
     *
     * Sets the matching node paired with this one.
     *
     * @access public
     * @param NodeInterface $node
     * @param string        $id
     */
    public function setMatchingNode(LexemeInterface $node, $id)
    {
        $this->matchingNode = $node;

        $this->id = $id;
    }

    /**
     *
     * @access public
     * @return bool
     */
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

    /**
     *
     * Returns the matching node paired with this one.
     *
     * @access public
     * @return NodeInterface
     */
    public function getMatchingNode()
    {
        return $this->matchingNode;
    }

    /**
     *
     * Use the param $checkMatching to further cascade the
     * check to the partner node linked in via $this->matchingNode.
     *
     * This cascading requires the check to prevent an infinite recursion.
     *
     * @access public
     * @param  bool $checkMatching
     * @return bool
     */
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

    /**
     *
     * @access public
     * @return bool
     */
    public function areAllParametersValid()
    {
        return true;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function extractParameters()
    {
        return true;
    }

    /**
     *
     * Runs all necessary checks to determine if this
     * lexeme is valid and checks them off as it goes.
     *
     * @access public
     * @param NodeInterface $lastValid
     */
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

    /**
     *
     * Sets this validation item from the checklist off as passing.
     *
     * @access public
     */
    public function passValidationForParam()
    {
        $this->validators['param'] = true;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function getValidationIsParamPassing()
    {
        return $this->validators['param'];
    }

    /**
     *
     * Sets this validation item from the checklist off as passing.
     *
     * @access public
     */
    public function passValidationForPairing()
    {
        $this->validators['pairing'] = true;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function getValidationIsPairingPassing()
    {
        return $this->validators['pairing'];
    }

    /**
     *
     * Sets this validation item from the checklist off as passing.
     *
     * @access public
     */
    public function passValidationForNestable()
    {
        $this->validators['nestable'] = true;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function getValidationIsNestablePassing()
    {
        return $this->validators['nestable'];
    }

    /**
     *
     * When debugging, call on dump() to view contents of
     * nodes recursively from the root node of the tree.
     *
     * @access public
     * @return string
     */
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

    /**
     *
     * Will return an array of errors concering why
     * this lexeme is unable to render itself.
     *
     * @access public
     * @return string
     */
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

    /**
     *
     * Renders an array of errors concering why
     * this lexeme is unable to render itself.
     *
     * @access public
     * @return string
     */
    public function renderErrors()
    {
        $errors = $this->getErrors();

        $message = '';

        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }

        $message = '<ul>' . $message . '</ul>';

        return '<span class="bb_invalid_tag" data-tip="bottom" title data-original-title="' . $message . '">' . htmlentities($this->lexingMatch, ENT_QUOTES) . '</span>';
    }

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
        if ($this->isValid(true)) {
            return static::$lexingHtml[$this->tokenIndex];
        }

        return $this->renderErrors();
    }
}
