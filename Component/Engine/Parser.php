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

namespace CCDNComponent\BBCodeBundle\Component\Engine;

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
class Parser
{
    /**
     *
     * @access protected
     */
    protected static $lexemeTable;

    /**
     *
     * @access public
     * @param LexemeTable $lexemeTable
     */
    public static function setLexemeTable($lexemeTable)
    {
        static::$lexemeTable = $lexemeTable;
    }

    /**
     *
     * @access public
     * @param  array  $tree
     * @return string
     */
    public function process($tree)
    {
        $html = $this->parse($tree);

        return '<div class="bb_wrapper"><pre>' . $html . '</pre></div>';
    }

    /**
     *
     * @access protected
     * @param  array  $tree
     * @return string
     */
    protected function parse($tree)
    {
        return $tree->cascadeRender();
    }
}
