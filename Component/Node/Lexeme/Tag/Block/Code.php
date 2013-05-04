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

namespace CCDNComponent\BBCodeBundle\Component\Node\Lexeme\Tag\Block;

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
class Code extends LexemeBase implements LexemeInterface
{
    /**
     *
     * @var string $canonicalLexemeName
     */
    protected static $canonicalLexemeName = 'Code';

    /**
     *
     * @var string $canonicalTokenName
     */
    protected static $canonicalTokenName = 'CODE';

    /**
     *
     * @var string $canonicalGroupName
     */
    protected static $canonicalGroupName = 'Block';

    /**
     *
     * 1) First level index should match the token
     *    index that the parameter will be found in.
     * 2) Second level index should specify the
     *    order of the parameter.
     *
     * @var array $parametersAcceptedOnToken
     */
    protected static $parametersAcceptedOnToken = array(0 => array(0 => 'lang'));

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
    protected static $isStandalone = false;

    /**
     *
     * Regular expressions to match against the
     * scan chunk during lexing process. The order
     * must match the $lexingHtml variable.
     *
     * @var array $lexingPattern
     */
    protected static $lexingPattern = array('/^\[CODE(?:\=(.*?)*)?\]$/', '/^\[\/CODE\]$/');

    /**
     *
     * HTML to output at the index of the matching regular
     * expression found in the $lexingPattern variable.
     *
     * Indexes between $lexingPattern and $lexingHtml must match.
     *
     * @var array $lexingHtml
     */
    protected static $lexingHtml = array('<div class="bbtag_code">{{ param[0] }}<pre><code class="cpp">', '</code></pre></div>');

    /**
     *
     * Specifies the array of other lexemes that
     * are permitted to be valid and rendered between
     * a matching pair of this particular lexeme.
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
            if ($this->tokenIndex == 0) {
                if (array_key_exists(0, $this->parameters)) {
                    $tab = '<ul class="nav nav-tabs"><li class="active"><a href="#"><bdi>' . htmlentities($this->parameters[0], ENT_QUOTES) . '</bdi></a></li></ul>';

                    return str_replace('{{ param[0] }}', $tab, static::$lexingHtml[$this->tokenIndex]);
                }

                return str_replace('{{ param[0] }}', '', static::$lexingHtml[$this->tokenIndex]);
            } else {
                return str_replace('{{ param[0] }}', '', static::$lexingHtml[$this->tokenIndex]);
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
            '*'
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
            '*'
        );
    }
}
