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
use CCDNComponent\BBCodeBundle\Component\Node\NodeBase;

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
abstract class LexemeBaseStatic extends NodeBase
{
    /**
     *
     * @var object $lexemeTable
     */
    protected static $lexemeTable;

    /**
     *
     * @var bool $isLexable
     */
    protected static $isLexable = true;

    /**
     *
     * @var bool $isStandalone
     */
    protected static $isStandalone = true;

    /**
     *
     * @var int $tokenCount
     */
    protected static $tokenCount = 0;

    /**
     *
     * @access public
     * @param  string          $lexingMatch
     * @return LexemeInterface
     */
    public static function createInstance($lexingMatch)
    {
        return new static($lexingMatch);
    }

    /**
     *
     * Set the lexeme table object that we will need
     * for checking allowed lexemes and also returning
     * new node instances of a given type.
     *
     * @access public
     * @param $lexemeTable
     */
    public static function setLexemeTable($lexemeTable)
    {
        static::$lexemeTable = $lexemeTable;
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalLexemeName()
    {
        return static::$canonicalLexemeName;
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalGroupName()
    {
        return static::$canonicalGroupName;
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalTokenName()
    {
        return static::$canonicalTokenName;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function getScanPattern()
    {
        return static::$scanPattern;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function isLexable()
    {
        return static::$isLexable;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function getLexingPattern()
    {
        return static::$lexingPattern;
    }

    /**
     *
     * @access public
     * @return int
     */
    public static function getTokenCount()
    {
        return static::$tokenCount;
    }

    /**
     *
     * As we extend the NodeBase, we must state if we are
     * a tree node or a lexeme node, which is important
     * during both validation and rendering cascading.
     *
     * @access public
     * @return bool
     */
    public static function isTree()
    {
        return false;
    }

    /**
     *
     * @access public
     * @return int
     */
    public static function isStandalone()
    {
        return static::$isStandalone;
    }

    /**
     *
     * Will check the input string against the array of lexing
     * patterns in the form of regex strings to find a match.
     * Returns true immediately when match is found.
     *
     * @access public
     * @param  string $lookupStr
     * @return bool
     */
    public static function isPatternMatch($lookupStr)
    {
        $canonicalLookupStr = strtoupper($lookupStr);

        foreach (static::$lexingPattern as $pattern) {
            if (preg_match($pattern, $canonicalLookupStr)) {
                return true;
            }
        }

        return false;
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

    /**
     *
     * Sets up to initial operations that only need
     * to be run once and stored in a static context.
     *
     * @access public
     */
    public static function warmup()
    {
        static::$tokenCount = count(static::$lexingPattern);

        static::compileAllowedSubNodeList();
    }

    /**
     *
     * Goes through the list of available lexemes and
     * checks them against white and black lists for
     * this given lexeme, if it is determined it is
     * allowed, then it is appended to the array of
     * allowed lexemes.
     *
     * @access public
     */
    public static function compileAllowedSubNodeList()
    {
        if (! static::isStandalone()) {
            $nodeGroupWhiteList = static::subNodeGroupWhiteList();
            $nodeGroupBlackList = static::subNodeGroupBlackList();
            $nodeWhiteList = static::subNodeWhiteList();
            $nodeBlackList = static::subNodeBlackList();

            $lexemes = static::$lexemeTable->getClassesArray();

            // Compile the list of 'allowed_nestable'
            foreach ($lexemes as $nestable) {

                // By default all tags can have nested content.
                // If a black list is defined, everything on the black-list is prevented from being nested.
                // If a white list is defined, groups on the white-list will override the black-list except individual tags.
                // To override a blacklisted group for a single tag, white list the tag.
                if (in_array($nestable::getCanonicalGroupName(), $nodeGroupBlackList) || in_array('*', $nodeGroupBlackList) || in_array('*', $nodeBlackList)) {
                    if (in_array($nestable::getCanonicalGroupName(), $nodeGroupWhiteList) || in_array($nestable::getCanonicalTokenName(), $nodeWhiteList)) {
                        if (! in_array($nestable::getCanonicalTokenName(), $nodeBlackList) || in_array($nestable::getCanonicalTokenName(), $nodeWhiteList)) {
                            static::$allowedNestable[] = $nestable::getCanonicalTokenName();
                        }
                     }
                } else {
                    if (in_array($nestable::getCanonicalGroupName(), $nodeGroupWhiteList) || in_array('*', $nodeGroupWhiteList) || in_array('*', $nodeWhiteList)) {
                         if (! in_array($nestable::getCanonicalTokenName(), $nodeBlackList)) {
                             static::$allowedNestable[] = $nestable::getCanonicalTokenName();
                         }
                     } else {
                        if (in_array($nestable::getCanonicalTokenName(), $nodeWhiteList)) {
                            static::$allowedNestable[] = $nestable::getCanonicalTokenName();
                        }
                     }
                }
            } // end foreach
        }
    }

    /**
     *
     * @access public
     * @param \CCDNComponent\BBCodeBundle\Component\Lexemes\LexemeInterface
     * @return bool
     */
    public static function childAllowed(LexemeInterface $lexeme)
    {
        if (in_array($lexeme->getCanonicalTokenName(), static::$allowedNestable)) {
            return true;
        }

        return false;
    }
}
