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

namespace CCDNComponent\BBCodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class CCDNComponentBBCodeBundle extends Bundle
{

    /**
     *
     * @access public
     */
    public function boot()
    {
        $twig = $this->container->get('twig');
        $twig->addGlobal('ccdn_component_bb_code', array(
            'editor' => array(
                'enable' => $this->container->getParameter('ccdn_component_bb_code.editor.enable'),
            ),
        ));
    }

}
