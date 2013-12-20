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

namespace CCDNComponent\BBCodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
class Configuration implements ConfigurationInterface
{
    /**
     *
     * @access public
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccdn_component_bb_code');

        $rootNode
            ->children()
            ->end();

        // Class file namespaces.
        $this
            ->addFormSection($rootNode)
            ->addComponentSection($rootNode)
        ;

        // Configuration stuff.
        $this
            ->addEditorSection($rootNode)
            ->addParserSection($rootNode)
        ;

        return $treeBuilder;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNComponent\BBCodeBundle\DependencyInjection\Configuration
     */
    private function addFormSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('type')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('bb_editor')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCodeBundle\Form\Type\BBEditorFormType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNComponent\BBCodeBundle\DependencyInjection\Configuration
     */
    private function addComponentSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('component')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('twig_extension')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('parse_bb')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCodeBundle\Component\TwigExtension\BBCodeExtension')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('chain')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('tag')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCodeBundle\Component\Chain\TagChain')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('acl')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCodeBundle\Component\Chain\ACLChain')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('bootstrap')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNComponent\BBCodeBundle\Component\BBCodeEngine')->end()
                            ->end()
                        ->end()
                        ->arrayNode('engine')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('bootstrap')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCode\Bootstrap')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('table_container')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCode\Engine\Table\TableContainer')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('scanner')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCode\Engine\Scanner')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('lexer')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCode\Engine\Lexer')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('parser')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNComponent\BBCode\Engine\Parser')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  ArrayNodeDefinition                                           $node
     * @return \CCDNComponent\BBCodeBundle\DependencyInjection\Configuration
     */
    private function addEditorSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->arrayNode('editor')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('enable')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  ArrayNodeDefinition                                           $node
     * @return \CCDNComponent\BBCodeBundle\DependencyInjection\Configuration
     */
    private function addParserSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->arrayNode('parser')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('enable')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }
}
