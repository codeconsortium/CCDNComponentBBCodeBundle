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

namespace CCDNComponent\BBCodeBundle\Component\Chain;

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
class TagChain
{
    /**
     *
     * @access private
     */
    private $tagIntegrators;

    /**
     *
     * @access public
     */
    public function __construct()
    {
        $this->tagIntegrators = array();
    }

    /**
     *
     * @access public
     * @param $tagIntegrator
     */
    public function addTagIntegrator($tagIntegrator)
    {
        $this->tagIntegrators[] = $tagIntegrator;
    }

    /**
     *
     * @access public
     * @return array
     */
    public function getTagIntegrators()
    {
        return $this->tagIntegrators;
    }
}
