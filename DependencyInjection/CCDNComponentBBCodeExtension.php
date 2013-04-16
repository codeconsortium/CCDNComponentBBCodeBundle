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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class CCDNComponentBBCodeExtension extends Extension
{
    /**
	 *
     * @access public
	 * @return string
     */
	public function getAlias()
	{
		return 'ccdn_component_bb_code';
	}
	
    /**
     *
     * @access public
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
		
		// Class file namespaces.
        $this
	        ->getFormSection($config, $container)
			->getComponentSection($config, $container)
		;
			
		// Configuration stuff.
		$this
			->getEditorSection($config, $container)
			->getParserSection($config, $container)
		;
		
		// Load Service definitions.
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
		
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNComponent\BBCodeBundle\DependencyInjection\CCDNComponentBBCodeExtension
     */
    private function getFormSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.form.type.bb_editor.class', $config['form']['type']['bb_editor']['class']);
		
		return $this;
	}

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNComponent\BBCodeBundle\DependencyInjection\CCDNComponentBBCodeExtension
     */
    private function getComponentSection(array $config, ContainerBuilder $container)
    {
//	    ccdn_component_bb_code.component.twig_extension.parse_bb.class:    CCDNComponent\BBCodeBundle\Component\TwigExtension\BBCodeExtension
//	    ccdn_component_bb_code.component.twig_extension.fetch_bb_tags.class:         CCDNComponent\BBCodeBundle\Extension\BBCodeFetchChoicesExtension
//	    ccdn_component_bb_code.component.engine.bootstrap.class:            CCDNComponent\BBCodeBundle\Component\Engine\BBCodeEngine
//	    ccdn_component_bb_code.component.engine.lexer.class:             CCDNComponent\BBCodeBundle\Component\Engine\Lexer
//	    ccdn_component_bb_code.component.engine.parser.class:            CCDNComponent\BBCodeBundle\Component\Engine\Parser
//	    ccdn_component_bb_code.component.engine.lexeme_table.class:      CCDNComponent\BBCodeBundle\Component\Engine\LexemeTable
//	    ccdn_component_bb_code.form.type.bb_editor.class: CCDNComponent\BBCodeBundle\Form\Type\BBEditorType
//        $container->setParameter('ccdn_component_bb_code.component.twig_extension.parse_bb.class', $config['component']['engine']['parse_bb']['class']);		
//        $container->setParameter('ccdn_component_bb_code.component.twig_extension.fetch_bb_tags.class', $config['component']['engine']['fetch_bb_tags']['class']);		
//        $container->setParameter('ccdn_component_bb_code.component.engine.bootstrap.class', $config['component']['engine']['bootstrap']['class']);		
//        $container->setParameter('ccdn_component_bb_code.component.engine.lexer.class', $config['component']['engine']['lexer']['class']);		
//        $container->setParameter('ccdn_component_bb_code.component.engine.parser.class', $config['component']['engine']['parser']['class']);		
//        $container->setParameter('ccdn_component_bb_code.component.engine.lexeme_table.class', $config['component']['engine']['lexeme_table']['class']);		
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNComponent\BBCodeBundle\DependencyInjection\CCDNComponentBBCodeExtension
     */
	private function getEditorSection(array $config, ContainerBuilder $container)
	{
		$container->setParameter('ccdn_component_bb_code.editor.enable', $config['editor']['enable']);
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNComponent\BBCodeBundle\DependencyInjection\CCDNComponentBBCodeExtension
     */
	private function getParserSection(array $config, ContainerBuilder $container)
	{
		$container->setParameter('ccdn_component_bb_code.parser.enable', $config['parser']['enable']);
		
		return $this;
	}
}
