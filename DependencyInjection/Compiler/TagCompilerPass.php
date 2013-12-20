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

namespace CCDNComponent\BBCodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

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
class TagCompilerPass implements CompilerPassInterface
{
    /**
     *
     * @access public
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('ccdn_component_bb_code.component.chain.tag')) {
            return;
        }

        $definition = $container->getDefinition('ccdn_component_bb_code.component.chain.tag');

        $taggedServices = $container->findTaggedServiceIds('ccdn_component_bb_code.tag');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addTagIntegrator', array(new Reference($id)));
        }
    }
}
