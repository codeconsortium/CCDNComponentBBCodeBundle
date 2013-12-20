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

namespace CCDNComponent\BBCodeBundle\Component\TwigExtension;

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
class BBCodeExtension extends \Twig_Extension
{
    /**
     *
     * @access protected
     */
    protected $engine;

    /**
     *
     * @access public
     * @param $engine
     * @param $enable
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    /**
     *
     * @access public
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'BBCode_Parse' => new \Twig_Function_Method($this, 'BBCodeParse'),
            'BBCode_IsParserEnabled' => new \Twig_Function_Method($this, 'BBCodeIsParserEnabled'),
            'BBCode_GetTagsAllowed' => new \Twig_Function_Method($this, 'BBCodeGetTagsAllowed'),
            'BBCode_GetTagsByGroup' => new \Twig_Function_Method($this, 'BBCodeGetTagsByGroup'),
            'BBCode_GetTokenName' => new \Twig_Function_Method($this, 'BBCodeGetTokenName'),
            'BBCode_GetTokenCount' => new \Twig_Function_Method($this, 'BBCodeGetTokenCount'),
            'BBCode_GetLexemeName' => new \Twig_Function_Method($this, 'BBCodeGetLexemeName'),
            'BBCode_GetButtonLabel' => new \Twig_Function_Method($this, 'BBCodeGetButtonLabel'),
            'BBCode_GetButtonIcon' => new \Twig_Function_Method($this, 'BBCodeGetButtonIcon'),
            'BBCode_GetButtonParameterQuestion' => new \Twig_Function_Method($this, 'BBCodeGetButtonParameterQuestion'),
        );
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'BBCode';
    }

    /**
     *
     * Converts BBCode to HTML via breaking down the BBCode tags into lexemes (via lexer method), which are
     * then stored in the form of an array, this array is then given to the parser which matches the proper
     * pairs to their HTML equivalent. Pairs must have a matching token, matched/unmatched are marked as
     * so during the lexing process via a reference token.
     *
     * @access public
     * @param  $input
     * @param string $tagGroup
     */
    public function BBCodeParse($input, $tableACLName = null)
    {
        return $this->engine->process($input, $tableACLName);
    }

    /**
     *
     * Determines if the parser is enabled based on global engine enabled state and the requested acl group.
     *
     * @access public
     * @param  string $aclName
     * @return bool
     */
    public function BBCodeIsParserEnabled($aclName)
    {
        $table = $this->engine->getTableACL($aclName);

        return $this->engine->isParserEnabled() && $table->isParserEnabled();
    }

    public function BBCodeGetTagsAllowed($aclName)
    {
        $table = $this->engine->getTableACL($aclName);

        return $table->getTags();
    }

    public function BBCodeGetTagsByGroup($aclName, $groupName)
    {
        $table = $this->engine->getTableACL($aclName);

        return $table->getTagsByGroup($groupName);
    }

    public function BBCodeGetTokenName($lexemeClass)
    {
        return $lexemeClass::getCanonicalTokenName();
    }

    public function BBCodeGetTokenCount($lexemeClass)
    {
        return $lexemeClass::getTokenCount();
    }

    public function BBCodeGetLexemeName($lexemeClass)
    {
        return $lexemeClass::getCanonicalLexemeName();
    }

    public function BBCodeGetButtonLabel($lexemeClass)
    {
        return $lexemeClass::getButtonLabel();
    }

    public function BBCodeGetButtonIcon($lexemeClass)
    {
        return $lexemeClass::getButtonIcon();
    }

    public function BBCodeGetButtonParameterQuestion($lexemeClass)
    {
        return $lexemeClass::getButtonParameterQuestion();
    }
}
