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

namespace CCDNComponent\BBCodeBundle\Component;

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
class BBCodeEngine
{
    protected $engine;
    protected $tableContainer;
    protected $isParserEnabled;

    public function __construct($engine, $isParserEnabled, $tableContainer, $tagChain, $aclChain)
    {
        $this->engine = $engine;

        $this->isParserEnabled = $isParserEnabled;

        $tagIntegrators = $tagChain->getTagIntegrators();

        foreach ($tagIntegrators as $tagIntegrator) {
            $tableContainer->setTableLexemes($tagIntegrator->build());
        }

        $aclIntegrators = $aclChain->getACLIntegrators();

        foreach ($aclIntegrators as $aclIntegrator) {
            $tableContainer->setTableACL($aclIntegrator->build());
        }

        $this->tableContainer = $tableContainer;
    }

    public function process($input, $tableACLName = null)
    {
        if ($this->isParserEnabled) {
            $html = $this->engine->process($input, $tableACLName);
        } else {
            $html = '<pre>' . htmlentities($input, ENT_QUOTES) . '</pre>';
        }

        return $html;
    }

    public function isParserEnabled()
    {
        return $this->isParserEnabled;
    }

    public function getTableACL($tableACLName)
    {
        return $this->engine->getTableACL($tableACLName);
    }
}
