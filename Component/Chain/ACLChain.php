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
class ACLChain
{
    /**
     *
     * @access private
     */
    private $tagACLIntegrators;

    /**
     *
     * @access public
     */
    public function __construct()
    {
        $this->tagACLIntegrators = array();
    }

    /**
     *
     * @access public
     * @param $tagACLIntegrator
     */
    public function addACLIntegrator($tagACLIntegrator)
    {
        $this->tagACLIntegrators[] = $tagACLIntegrator;
    }

    /**
     *
     * @access public
     * @return array
     */
    public function getACLIntegrators()
    {
        return $this->tagACLIntegrators;
    }
}
