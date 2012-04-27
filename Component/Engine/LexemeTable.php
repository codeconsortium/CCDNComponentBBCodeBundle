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
		
		$basePath = $this->container->get('request')->getBasePath();
		$smileys = $basePath . '/bundles/ccdncomponentbbcode/images/smilies/';
		
		
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

			//
			// Smileys
			//			
			array(
				'symbol_lexeme' => 'angel',
				'symbol_token' => array('/\:angel\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'angel.gif" />'),
			),
			array(
				'symbol_lexeme' => 'arms',
				'symbol_token' => array('/\:arms\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'arms.gif" />'),
			),
			array(
				'symbol_lexeme' => 'atomic',
				'symbol_token' => array('/\:atomic\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'atomic.gif" />'),
			),
			array(
				'symbol_lexeme' => 'badday',
				'symbol_token' => array('/\:badday\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'badday.gif" />'),
			),
			array(
				'symbol_lexeme' => 'baloon',
				'symbol_token' => array('/\:baloon\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'baloon.gif" />'),
			),
			array(
				'symbol_lexeme' => 'banned',
				'symbol_token' => array('/\:banned\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'banned.gif" />'),
			),
			array(
				'symbol_lexeme' => 'bash',
				'symbol_token' => array('/\:bash\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'bash.gif" />'),
			),
			array(
				'symbol_lexeme' => 'biggrin',
				'symbol_token' => array('/\:biggrin\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'biggrin.gif" />'),
			),
			array(
				'symbol_lexeme' => 'bleh',
				'symbol_token' => array('/\:bleh\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'bleh.gif" />'),
			),
			array(
				'symbol_lexeme' => 'blink',
				'symbol_token' => array('/\:blink\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'blink.gif" />'),
			),
			array(
				'symbol_lexeme' => 'bop',
				'symbol_token' => array('/\:bop\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'bop.gif" />'),
			),
			array(
				'symbol_lexeme' => 'bouncy',
				'symbol_token' => array('/\:bouncy\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'bouncy.gif" />'),
			),
			array(
				'symbol_lexeme' => 'bye',
				'symbol_token' => array('/\:bye\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'bye.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cake',
				'symbol_token' => array('/\:cake\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cake.gif" />'),
			),
			array(
				'symbol_lexeme' => 'chaplin',
				'symbol_token' => array('/\:chaplin\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'chaplin.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cheekkiss',
				'symbol_token' => array('/\:cheekkiss\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cheekkiss.gif" />'),
			),
			array(
				'symbol_lexeme' => 'chin',
				'symbol_token' => array('/\:chin\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'chin.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cloud9',
				'symbol_token' => array('/\:cloud9\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cloud9.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cool',
				'symbol_token' => array('/\:cool\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cool.gif" />'),
			),
			array(
				'symbol_lexeme' => 'crossbones',
				'symbol_token' => array('/\:crossbones\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'crossbones.gif" />'),
			),
			array(
				'symbol_lexeme' => 'crutches',
				'symbol_token' => array('/\:crutches\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'crutches.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cry',
				'symbol_token' => array('/\:cry\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cry.gif" />'),
			),
			array(
				'symbol_lexeme' => 'cupid',
				'symbol_token' => array('/\:cupid\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'cupid.gif" />'),
			),
			array(
				'symbol_lexeme' => 'drinks',
				'symbol_token' => array('/\:drinks\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'drinks.gif" />'),
			),
			array(
				'symbol_lexeme' => 'drool',
				'symbol_token' => array('/\:drool\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'drool.gif" />'),
			),
			array(
				'symbol_lexeme' => 'dry',
				'symbol_token' => array('/\:dry\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'dry.gif" />'),
			),
			array(
				'symbol_lexeme' => 'dunce',
				'symbol_token' => array('/\:dunce\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'dunce.gif" />'),
			),
			array(
				'symbol_lexeme' => 'excited',
				'symbol_token' => array('/\:excited\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'excited.gif" />'),
			),
			array(
				'symbol_lexeme' => 'explode',
				'symbol_token' => array('/\:explode\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'explode.gif" />'),
			),
			array(
				'symbol_lexeme' => 'fart',
				'symbol_token' => array('/\:fart\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'fart.gif" />'),
			),
			array(
				'symbol_lexeme' => 'flowers',
				'symbol_token' => array('/\:flowers\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'flowers.gif" />'),
			),
			array(
				'symbol_lexeme' => 'goodgrief',
				'symbol_token' => array('/\:goodgrief\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'goodgrief.gif" />'),
			),
			array(
				'symbol_lexeme' => 'happyno',
				'symbol_token' => array('/\:happyno\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'happyno.gif" />'),
			),
			array(
				'symbol_lexeme' => 'happyyes',
				'symbol_token' => array('/\:happyyes\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'happyyes.gif" />'),
			),
			array(
				'symbol_lexeme' => 'hug',
				'symbol_token' => array('/\:hug\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'hug.gif" />'),
			),
			array(
				'symbol_lexeme' => 'huglove',
				'symbol_token' => array('/\:huglove\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'huglove.gif" />'),
			),
			array(
				'symbol_lexeme' => 'huh',
				'symbol_token' => array('/\:huh\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'huh.gif" />'),
			),
			array(
				'symbol_lexeme' => 'interest',
				'symbol_token' => array('/\:interest\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'interest.gif" />'),
			),
			array(
				'symbol_lexeme' => 'jawdrop',
				'symbol_token' => array('/\:jawdrop\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'jawdrop.gif" />'),
			),
			array(
				'symbol_lexeme' => 'jumpy',
				'symbol_token' => array('/\:jumpy\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'jumpy.gif" />'),
			),
			array(
				'symbol_lexeme' => 'knight',
				'symbol_token' => array('/\:knight\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'knight.gif" />'),
			),
			array(
				'symbol_lexeme' => 'laugh',
				'symbol_token' => array('/\:laugh\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'laugh.gif" />'),
			),
			array(
				'symbol_lexeme' => 'locked',
				'symbol_token' => array('/\:locked\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'locked.gif" />'),
			),
			array(
				'symbol_lexeme' => 'lol',
				'symbol_token' => array('/\:lol\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'lol.gif" />'),
			),
			array(
				'symbol_lexeme' => 'mad',
				'symbol_token' => array('/\:mad\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'mad.gif" />'),
			),
			array(
				'symbol_lexeme' => 'mellow',
				'symbol_token' => array('/\:mellow\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'mellow.gif" />'),
			),
			array(
				'symbol_lexeme' => 'nudgewink',
				'symbol_token' => array('/\:nudgewink\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'nudgewink.gif" />'),
			),
			array(
				'symbol_lexeme' => 'ohmy',
				'symbol_token' => array('/\:ohmy\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'ohmy.gif" />'),
			),
			array(
				'symbol_lexeme' => 'ouch',
				'symbol_token' => array('/\:ouch\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'ouch.gif" />'),
			),
			array(
				'symbol_lexeme' => 'pcwhack',
				'symbol_token' => array('/\:pcwhack\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'pcwhack.gif" />'),
			),
			array(
				'symbol_lexeme' => 'pirate',
				'symbol_token' => array('/\:pirate\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'pirate.gif" />'),
			),
			array(
				'symbol_lexeme' => 'poke',
				'symbol_token' => array('/\:poke\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'poke.gif" />'),
			),
			array(
				'symbol_lexeme' => 'popcorn',
				'symbol_token' => array('/\:popcorn\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'popcorn.gif" />'),
			),
			array(
				'symbol_lexeme' => 'puke',
				'symbol_token' => array('/\:puke\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'puke.gif" />'),
			),
			array(
				'symbol_lexeme' => 'rant',
				'symbol_token' => array('/\:rant\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'rant.gif" />'),
			),
			array(
				'symbol_lexeme' => 'rip',
				'symbol_token' => array('/\:rip\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'rip.gif" />'),
			),
			array(
				'symbol_lexeme' => 'rofl',
				'symbol_token' => array('/\:rofl\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'rofl.gif" />'),
			),
			array(
				'symbol_lexeme' => 'rolleyes',
				'symbol_token' => array('/\:rolleyes\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'rolleyes.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sad',
				'symbol_token' => array('/\:sad\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sad.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sadangel',
				'symbol_token' => array('/\:sadangel\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sadangel.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sadno',
				'symbol_token' => array('/\:sadno\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sadno.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sex',
				'symbol_token' => array('/\:sex\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sex.gif" />'),
			),
			array(
				'symbol_lexeme' => 'shy',
				'symbol_token' => array('/\:shy\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'shy.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sick',
				'symbol_token' => array('/\:sick\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sick.gif" />'),
			),
			array(
				'symbol_lexeme' => 'slaphead',
				'symbol_token' => array('/\:slaphead\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'slaphead.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sleep',
				'symbol_token' => array('/\:sleep\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sleep.gif" />'),
			),
			array(
				'symbol_lexeme' => 'smile',
				'symbol_token' => array('/\:smile\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'smile.gif" />'),
			),
			array(
				'symbol_lexeme' => 'smug',
				'symbol_token' => array('/\:smug\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'smug.gif" />'),
			),
			array(
				'symbol_lexeme' => 'spock',
				'symbol_token' => array('/\:spock\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'spock.gif" />'),
			),
			array(
				'symbol_lexeme' => 'stamp',
				'symbol_token' => array('/\:stamp\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'stamp.gif" />'),
			),
			array(
				'symbol_lexeme' => 'stereo',
				'symbol_token' => array('/\:stereo\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'stereo.gif" />'),
			),
			array(
				'symbol_lexeme' => 'stretcher',
				'symbol_token' => array('/\:stretcher\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'stretcher.gif" />'),
			),
			array(
				'symbol_lexeme' => 'sweat',
				'symbol_token' => array('/\:sweat\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'sweat.gif" />'),
			),
			array(
				'symbol_lexeme' => 'tears',
				'symbol_token' => array('/\:tears\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'tears.gif" />'),
			),
			array(
				'symbol_lexeme' => 'therethere',
				'symbol_token' => array('/\:therethere\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'therethere.gif" />'),
			),
			array(
				'symbol_lexeme' => 'thumbsup',
				'symbol_token' => array('/\:thumbsup\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'thumbsup.gif" />'),
			),
			array(
				'symbol_lexeme' => 'tomatoes',
				'symbol_token' => array('/\:tomatoes\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'tomatoes.gif" />'),
			),
			array(
				'symbol_lexeme' => 'tongue',
				'symbol_token' => array('/\:tongue\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'tongue.gif" />'),
			),
			array(
				'symbol_lexeme' => 'ty',
				'symbol_token' => array('/\:ty\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'ty.gif" />'),
			),
			array(
				'symbol_lexeme' => 'unsure',
				'symbol_token' => array('/\:unsure\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'unsure.gif" />'),
			),
			array(
				'symbol_lexeme' => 'wave',
				'symbol_token' => array('/\:wave\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'wave.gif" />'),
			),
			array(
				'symbol_lexeme' => 'welcome',
				'symbol_token' => array('/\:welcome\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'welcome.gif" />'),
			),
			array(
				'symbol_lexeme' => 'what',
				'symbol_token' => array('/\:what\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'what.gif" />'),
			),
			array(
				'symbol_lexeme' => 'whip',
				'symbol_token' => array('/\:whip\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'whip.gif" />'),
			),
			array(
				'symbol_lexeme' => 'wiggle',
				'symbol_token' => array('/\:wiggle\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'wiggle.gif" />'),
			),
			array(
				'symbol_lexeme' => 'wink',
				'symbol_token' => array('/\:wink\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'wink.gif" />'),
			),
			array(
				'symbol_lexeme' => 'worthy',
				'symbol_token' => array('/\:worthy\:/'),
				'symbol_html' => array('<img src="' . $smileys . 'worthy.gif" />'),
			),
		);
		
		foreach($this->lexemes as $key => &$lexeme)
		{
			$lexeme['token_count'] = count($lexeme['symbol_token']);
		}
		
		return $this->lexemes;
	}
	
}