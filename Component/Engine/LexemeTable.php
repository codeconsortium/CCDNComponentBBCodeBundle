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

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class LexemeTable extends ContainerAware
{

	/**
	 *
	 * @access protected
	 */
	protected $lexemes;


	/**
	 *
	 * @access protected
	 */
	protected $container;
	
	
	/**
	 *
	 * @access public
	 */
	public function __construct($service_container)
	{
		$this->container = $service_container;
		
	}
	
	
	/**
	 *
	 * @access public
	 * @return Array $lexemes
	 */
	public function &getLexemes()
	{
		
		$label_said = $this->container->get('translator')->trans('bb_code.quote.said', array(), 'CCDNComponentBBCodeBundle');
		$label_code = $this->container->get('translator')->trans('bb_code.code', array(), 'CCDNComponentBBCodeBundle');
		
		
		
		$this->lexemes = array(
			array(	'symbol_lexeme' => 'quote',
					'symbol_token' => array('/(\[QUOTE?(\=[\P{C}\p{Cc}]*)*\])/', '/(\[\/QUOTE\])/'),
					'symbol_html' => array('</span><div class="bb_box"><div class="bb_tag_head_strip">{{param}} ' . $label_said . ':</div><div class="bb_tag_quote"><pre>', '</pre></div></div><span class="common_body">'),
					'use_pre_tag' => true,
			),
			array(	'symbol_lexeme' => 'code',
					'symbol_token' => array('/(\[CODE?(\=[\P{C}\p{Cc}]*)*\])/', '/(\[\/CODE\])/'),
					'symbol_html' => array('</span><div class="bb_box"><div class="bb_tag_head_strip">' . $label_code . ': {{param}}</div><div class="bb_tag_code"><pre class="bb_tag_code">', '</pre></div></div><span class="common_body">'),
					'use_pre_tag' => true,
					'use_nested' => false,
			),	
			array(	'symbol_lexeme' => 'bold',
					'symbol_token' => array('/(\[B\])/', '/(\[\/B\])/'),
					'symbol_html' => array('<b>', '</b>'),
			),
			array(	'symbol_lexeme' => 'underline',
					'symbol_token' => array('/(\[U\])/', '/(\[\/U\])/'),
					'symbol_html' => array('<u>', '</u>'),
			),
			array(	'symbol_lexeme' => 'italics',
					'symbol_token' => array('/(\[I\])/', '/(\[\/I\])/'),
					'symbol_html' => array('<i>', '</i>'),
			),
			array(	'symbol_lexeme' => 'style',
					'symbol_token' => array('/(\[STYLE?(\=[a-zA-Z0-9 ]*)*\])/', '/(\[\/STYLE\])/'),
					'symbol_html' => array('</span><span class="{{param}}">', '</span><span class="common_body">'),
					'param_choices' => array('title' => 'bb_tag_style_title', 'heading' => 'bb_tag_style_heading', 'sub section' => 'bb_tag_style_sub_section', 'body' => 'bb_tag_style_body'),
			),
			array(	'symbol_lexeme' => 'subscript',
					'symbol_token' => array('/(\[SUB\])/', '/(\[\/SUB\])/'),
					'symbol_html' => array('<sub>', '</sub>'),
			),
			array(	'symbol_lexeme' => 'superscript',
					'symbol_token' => array('/(\[SUP\])/', '/(\[\/SUP\])/'),
					'symbol_html' => array('<sup>', '</sup>'),
			),
			array(	'symbol_lexeme' => 'strikethrough',
					'symbol_token' => array('/(\[STRIKE\])/', '/(\[\/STRIKE\])/'),
					'symbol_html' => array('<del>', '</del>'),
			),
			array(	'symbol_lexeme' => 'url',
					'symbol_token' => array('/(\[URL?(\=(.*?)*)\])/', '/(\[\/URL\])/'),
					'symbol_html' => array('<a href="{{param}}" target="_blank">', '</a>'),
					'param_is_url' => true,
			),
			array(	'symbol_lexeme' => 'image',
					'symbol_token' => array('/(\[IMG?(\=(.*?)*)\])/', '/(\[\/IMG\])/'),
					'symbol_html' => array('<img class="bb_tag_img" src="{{param}}" alt="User contributed image: ', '">'),
					'param_is_url' => true,
			),
            array(	'symbol_lexeme' => 'youtube',
                    'symbol_token' => array('/(\[YOUTUBE?(\=[\P{C}\p{Cc}]*)*\])/', '/(\[\/YOUTUBE\])/'),
                    'symbol_html' => array('</span><iframe width="560" height="315" src="http://www.youtube.com/embed/', '" frameborder="0" allowfullscreen></iframe><span class="common_body">'),
            ),
            array(	'symbol_lexeme' => 'vimeo',
                    'symbol_token' => array('/(\[VIMEO?(\=[\P{C}\p{Cc}]*)*\])/', '/(\[\/VIMEO\])/'),
                    'symbol_html' => array('</span><iframe src="http://player.vimeo.com/video/', '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="400" height="300" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe><span class="common_body">'),
            ),
		);
		
		foreach($this->lexemes as $key => &$lexeme)
		{
			$lexeme['token_count'] = count($lexeme['symbol_token']);
		}
		
		return $this->lexemes;
	}
	
}