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
	 * @param $container
	 */
	public function __construct($container)
	{
		$this->container = $container;

		$this->process();		
	}

	
	public function getLexeme($lexemeName)
	{
		return $this->lexemes[$lexemeName];
	}
	
	
	/**
	 *
	 * Matches a lookup str against the lexemes until a match is found.
	 *
	 * @access private
	 * @param array $lexemes, string $lookup
	 * @return array $lookup
	 */
	public function lookup($lookupStr)
	{
		foreach ($this->lexemes as $lexemeKey => $lexeme)
		{
			foreach ($lexeme['symbol_token'] as $tokenKey => $token)
			{
				if (preg_match($token, $lookupStr))
				{
					return array(
						'lookup_str' 	=> $lookupStr,
						'lexeme_key'	=> $lexemeKey,
						'token_key'		=> $tokenKey,
					);
				}
			}
		}
		
		return $lookupStr;
	}
	
	
	/**
	 *
	 * @access public
	 * @return Array $lexemes
	 */
	private function process()
	{		
		$labelSaid = $this->container->get('translator')->trans('ccdn_component_bb_code.parser.quote_said', array(), 'CCDNComponentBBCodeBundle');
		$labelCode = $this->container->get('translator')->trans('ccdn_component_bb_code.parser.code', array(), 'CCDNComponentBBCodeBundle');
		
		$basePath = dirname($_SERVER['REQUEST_URI']); //$this->container->get('request')->getBasePath();
		$smileys = $basePath . '/bundles/ccdncomponentbbcode/images/smilies/';
		
		// 
		// By default all tags can have nested content.
		// If a black list is defined, everything on the black-list is prevented from being nested.
		// If a white list is defined, everything on the white-list will override the black-list.
		//
		$lexemes = array(
			'QUOTE' => array(
					'symbol_lexeme' => 'QUOTE',
					'symbol_token' => array('/^\[QUOTE?(\=[\P{C}\p{Cc}]*)*\]$/', '/^\[\/QUOTE\]$/'),
					'symbol_html' => array('</span><div class="bb_box"><div class="bb_tag_head_strip">{{param}} ' . $labelSaid . ':</div><pre>', '</pre></div><span class="common_body">'),
					'group' => 'block',
					'black_list' => array('groups' => array('asset'), 'tags' => array('CODE')),
					'white_list' => array('groups' => array('format', 'smiley'), 'tags' => array()),
					'use_pre_tag' => true,
					'accepts_param' => true,
					'param_required' => false,
			),
			'CODE' => array(
					'symbol_lexeme' => 'CODE',
					'symbol_token' => array('/^\[CODE?(\=[\P{C}\p{Cc}]*)*\]$/', '/^\[\/CODE\]$/'),
					'symbol_html' => array('</span><div class="bb_box"><div class="bb_tag_head_strip">' . $labelCode . ': {{param}}</div><pre>', '</pre></div><span class="common_body">'),
					'group' => 'block',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array()),
					'use_pre_tag' => true,
					'accepts_param' => true,
					'param_required' => false,
			),
			
			
            'YOUTUBE' => array(
					'symbol_lexeme' => 'YOUTUBE',
                    'symbol_token' => array('/^\[YOUTUBE?(\=[\P{C}\p{Cc}]*)*\]$/'),
                    'symbol_html' => array('</span><iframe width="560" height="315" src="http://www.youtube.com/embed/{{param}}" frameborder="0" allowfullscreen></iframe><span class="common_body">'),
					'group' => 'asset',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array()),
					'accepts_param' => true,
					'param_required' => true,
            ),
            'VIMEO' => array(
					'symbol_lexeme' => 'VIMEO',
                    'symbol_token' => array('/^\[VIMEO?(\=[\P{C}\p{Cc}]*)*\]$/'),
                    'symbol_html' => array('</span><iframe src="http://player.vimeo.com/video/{{param}}?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="400" height="300" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe><span class="common_body">'),
					'group' => 'asset',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array()),
					'accepts_param' => true,
					'param_required' => true,
            ),
			'IMG' => array(	
					'symbol_lexeme' => 'IMG',
					'symbol_token' => array('/^\[IMG?(\=(.*?)*)\]$/', '/^\[\/IMG\]$/'),
					'symbol_html' => array('<img class="bb_tag_img" src="{{param}}" alt="User contributed image: ', '">'),
					'group' => 'asset',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array()),
					'param_is_url' => true,
			),


//			'STYLE' => array(
//					'symbol_lexeme' => 'STYLE',
//					'symbol_token' => array('/^\[STYLE?(\=[a-zA-Z0-9 ]*)*\]$/', '/^\[\/STYLE\]$/'),
//					'symbol_html' => array('</span><span class="{{param}}">', '</span><span class="common_body">'),
//					'group' => 'format',
//					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
//					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
//					'accepts_param' => true,
//					'param_required' => true,
//					'param_choices' => array('title' => 'bb_tag_style_title', 'heading' => 'bb_tag_style_heading', 'sub section' => 'bb_tag_style_sub_section', 'body' => 'bb_tag_style_body'),
//			),
			'H1' => array(
					'symbol_lexeme' => 'H1',
					'symbol_token' => array('/^\[H1\]$/', '/^\[\/H1\]$/'),
					'symbol_html' => array('</span><h1>', '</h1><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('format'), 'tags' => array('IMG')),
			),
			'H2' => array(
					'symbol_lexeme' => 'H2',
					'symbol_token' => array('/^\[H2\]$/', '/^\[\/H2\]$/'),
					'symbol_html' => array('</span><h2>', '</h2><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('format'), 'tags' => array('IMG')),
			),
			'H3' => array(
					'symbol_lexeme' => 'H3',
					'symbol_token' => array('/^\[H3\]$/', '/^\[\/H3\]$/'),
					'symbol_html' => array('</span><h3>', '</h3><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('format'), 'tags' => array('IMG')),
			),
			'URL' => array(
					'symbol_lexeme' => 'URL',
					'symbol_token' => array('/^\[URL?(\=(.*?)*)\]$/', '/^\[\/URL\]$/'),
					'symbol_html' => array('<a href="{{param}}" target="_blank">', '</a>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array('URL')),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
					'accepts_param' => true,
					'param_required' => true,
					'param_is_url' => true,
			),	
			'UL' => array(
					'symbol_lexeme' => 'UL',
					'symbol_token' => array('/^\[UL\]$/', '/^\[\/UL\]$/'),
					'symbol_html' => array('</span><ul>', '</ul><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array('LI')),
			),
			'OL' => array(
					'symbol_lexeme' => 'OL',
					'symbol_token' => array('/^\[OL\]$/', '/^\[\/OL\]$/'),
					'symbol_html' => array('</span><ol>', '</ol><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('*'), 'tags' => array()),
					'white_list' => array('groups' => array(), 'tags' => array('LI')),
			),
			'LI' => array(
					'symbol_lexeme' => 'LI',
					'symbol_token' => array('/^\[LI\]$/', '/^\[\/LI\]$/'),
					'symbol_html' => array('</span><li>', '</li><span class="common_body">'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array('H1', 'H2', 'H3')),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array('IMG')),
			),
			'B' => array(
					'symbol_lexeme' => 'B',
					'symbol_token' => array('/^\[B\]$/', '/^\[\/B\]$/'),
					'symbol_html' => array('<b>', '</b>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),
			'U' => array(
					'symbol_lexeme' => 'U',
					'symbol_token' => array('/^\[U\]$/', '/^\[\/U\]$/'),
					'symbol_html' => array('<u>', '</u>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),
			'I' => array(
					'symbol_lexeme' => 'I',
					'symbol_token' => array('/^\[I\]$/', '/^\[\/I\]$/'),
					'symbol_html' => array('<i>', '</i>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),
			'SUB' => array(
					'symbol_lexeme' => 'SUB',
					'symbol_token' => array('/^\[SUB\]$/', '/^\[\/SUB\]$/'),
					'symbol_html' => array('<sub>', '</sub>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),
			'SUP' => array(
					'symbol_lexeme' => 'SUP',
					'symbol_token' => array('/^\[SUP\]$/', '/^\[\/SUP\]$/'),
					'symbol_html' => array('<sup>', '</sup>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),
			'STRIKE' => array(
					'symbol_lexeme' => 'STRIKE',
					'symbol_token' => array('/^\[STRIKE\]$/', '/^\[\/STRIKE\]$/'),
					'symbol_html' => array('<del>', '</del>'),
					'group' => 'format',
					'black_list' => array('groups' => array('block', 'asset'), 'tags' => array()),
					'white_list' => array('groups' => array('smiley', 'format'), 'tags' => array()),
			),


			//
			// Smileys
			//
			':SMILE:' => array(
				'symbol_lexeme' => ':SMILE:',
				'symbol_token' => array('/^\[\:SMILE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'smile.gif" alt="Smile">'),
				'group' => 'smiley',
			),
			':SMUG:' => array(
				'symbol_lexeme' => ':SMUG:',
				'symbol_token' => array('/^\[\:SMUG\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'smug.gif" alt="Smug">'),
				'group' => 'smiley',
			),
			':INTEREST:' => array(
				'symbol_lexeme' => ':INTEREST:',
				'symbol_token' => array('/^\[\:INTEREST\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'interest.gif" alt="Interest">'),
				'group' => 'smiley',
			),
			':TONGUE:' => array(
				'symbol_lexeme' => ':TONGUE:',
				'symbol_token' => array('/^\[\:TONGUE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'tongue.gif" alt="Tongue">'),
				'group' => 'smiley',
			),
			':COOL:' => array(
				'symbol_lexeme' => ':COOL:',
				'symbol_token' => array('/^\[\:COOL\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'cool.gif" alt="Cool">'),
				'group' => 'smiley',
			),
			':BIGGRIN:' => array(
				'symbol_lexeme' => ':BIGGRIN:',
				'symbol_token' => array('/^\[\:BIGGRIN\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'biggrin.gif" alt="Big-grin">'),
				'group' => 'smiley',
			),
			':LAUGH:' => array(
				'symbol_lexeme' => ':LAUGH:',
				'symbol_token' => array('/^\[\:LAUGH\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'laugh.gif" alt="Laugh">'),
				'group' => 'smiley',
			),
			':LOL:' => array(
				'symbol_lexeme' => ':LOL:',
				'symbol_token' => array('/^\[\:LOL\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'lol.gif" alt="LOL">'),
				'group' => 'smiley',
			),
			':ROLLEYES:' => array(
				'symbol_lexeme' => ':ROLLEYES:',
				'symbol_token' => array('/^\[\:ROLLEYES\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'rolleyes.gif" alt="Roll-eyes">'),
				'group' => 'smiley',
			),
			':SWEAT:' => array(
				'symbol_lexeme' => ':SWEAT:',
				'symbol_token' => array('/^\[\:SWEAT\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'sweat.gif" alt="Sweat">'),
				'group' => 'smiley',
			),
			':TY:' => array(
				'symbol_lexeme' => ':TY:',
				'symbol_token' => array('/^\[\:TY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'ty.gif" alt="TY">'),
				'group' => 'smiley',
			),
			':HAPPYNO:' => array(
				'symbol_lexeme' => ':HAPPYNO:',
				'symbol_token' => array('/^\[\:HAPPYNO\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'happyno.gif" alt="Happy no">'),
				'group' => 'smiley',
			),
			':HAPPYYES:' => array(
				'symbol_lexeme' => ':HAPPYYES:',
				'symbol_token' => array('/^\[\:HAPPYYES\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'happyyes.gif" alt="Happy yes">'),
				'group' => 'smiley',
			),
			':DRY:' => array(
				'symbol_lexeme' => ':DRY:',
				'symbol_token' => array('/^\[\:DRY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'dry.gif" alt="Dry">'),
				'group' => 'smiley',
			),
			':GOODGRIEF:' => array(
				'symbol_lexeme' => ':GOODGRIEF:',
				'symbol_token' => array('/^\[\:GOODGRIEF\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'goodgrief.gif" alt="Good-grief">'),
				'group' => 'smiley',
			),
			':HUH:' => array(
				'symbol_lexeme' => ':HUH:',
				'symbol_token' => array('/^\[\:HUH\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'huh.gif" alt="Huh">'),
				'group' => 'smiley',
			),
			':UNSURE:' => array(
				'symbol_lexeme' => ':UNSURE:',
				'symbol_token' => array('/^\[\:UNSURE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'unsure.gif" alt="Unsure">'),
				'group' => 'smiley',
			),
			':BLINK:' => array(
				'symbol_lexeme' => ':BLINK:',
				'symbol_token' => array('/^\[\:BLINK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'blink.gif" alt="Blink">'),
				'group' => 'smiley',
			),
			':BLEH:' => array(
				'symbol_lexeme' => ':BLEH:',
				'symbol_token' => array('/^\[\:BLEH\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'bleh.gif" alt="Bleh">'),
				'group' => 'smiley',
			),
			':MELLOW:' => array(
				'symbol_lexeme' => ':MELLOW:',
				'symbol_token' => array('/^\[\:MELLOW\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'mellow.gif" alt="Mellow">'),
				'group' => 'smiley',
			),
			':WINK:' => array(
				'symbol_lexeme' => ':WINK:',
				'symbol_token' => array('/^\[\:WINK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'wink.gif" alt="Wink">'),
				'group' => 'smiley',
			),			
			':SPOCK:' => array(
				'symbol_lexeme' => ':SPOCK:',
				'symbol_token' => array('/^\[\:SPOCK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'spock.gif" alt="Spock">'),
				'group' => 'smiley',
			),
			':SHY:' => array(
				'symbol_lexeme' => ':SHY:',
				'symbol_token' => array('/^\[\:SHY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'shy.gif" alt="Shy">'),
				'group' => 'smiley',
			),
			':SAD:' => array(
				'symbol_lexeme' => ':SAD:',
				'symbol_token' => array('/^\[\:SAD\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'sad.gif" alt="Sad">'),
				'group' => 'smiley',
			),

			':SADNO:' => array(
				'symbol_lexeme' => ':SADNO:',
				'symbol_token' => array('/^\[\:SADNO\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'sadno.gif" alt="Sad no">'),
				'group' => 'smiley',
			),
			':TEARS:' => array(
				'symbol_lexeme' => ':TEARS:',
				'symbol_token' => array('/^\[\:TEARS\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'tears.gif" alt="Tears">'),
				'group' => 'smiley',
			),
			':CRY:' => array(
				'symbol_lexeme' => ':CRY:',
				'symbol_token' => array('/^\[\:CRY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'cry.gif" alt="Cry">'),
				'group' => 'smiley',
			),
			':OHMY:' => array(
				'symbol_lexeme' => ':OHMY:',
				'symbol_token' => array('/^\[\:OHMY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'ohmy.gif" alt="Oh-my">'),
				'group' => 'smiley',
			),
			':NUDGEWINK:' => array(
				'symbol_lexeme' => ':NUDGEWINK:',
				'symbol_token' => array('/^\[\:NUDGEWINK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'nudgewink.gif" alt="Nudge-wink">'),
				'group' => 'smiley',
			),
			':MAD:' => array(
				'symbol_lexeme' => ':MAD:',
				'symbol_token' => array('/^\[\:MAD\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'mad.gif" alt="Mad">'),
				'group' => 'smiley',
			),
			':RANT:' => array(
				'symbol_lexeme' => ':RANT:',
				'symbol_token' => array('/^\[\:RANT\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'rant.gif" alt="Rant">'),
				'group' => 'smiley',
			),
			':SICK:' => array(
				'symbol_lexeme' => ':SICK:',
				'symbol_token' => array('/^\[\:SICK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'sick.gif" alt="Sick">'),
				'group' => 'smiley',
			),

			//
			// 20-25px approx
			//
			':ANGEL:' => array(
				'symbol_lexeme' => ':ANGEL:',
				'symbol_token' => array('/^\[\:ANGEL\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'angel.gif" alt="Angel">'),
				'group' => 'smiley',
			),
			':CHIN:' => array(
				'symbol_lexeme' => ':CHIN:',
				'symbol_token' => array('/^\[\:CHIN\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'chin.gif" alt="Chin">'),
				'group' => 'smiley',
			),
			':CROSSBONES:' => array(
				'symbol_lexeme' => ':CROSSBONES:',
				'symbol_token' => array('/^\[\:CROSSBONES\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'crossbones.gif" alt="Crossbones">'),
				'group' => 'smiley',
			),

			':CAKE:' => array(
				'symbol_lexeme' => ':CAKE:',
				'symbol_token' => array('/^\[\:CAKE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'cake.gif" alt="Cake">'),
				'group' => 'smiley',
			),
			':LOCKED:' => array(
				'symbol_lexeme' => ':LOCKED:',
				'symbol_token' => array('/^\[\:LOCKED\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'locked.gif" alt="Locked">'),
				'group' => 'smiley',
			),
			':BASH:' => array(
				'symbol_lexeme' => ':BASH:',
				'symbol_token' => array('/^\[\:BASH\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'bash.gif" alt="Bash">'),
				'group' => 'smiley',
			),
			':EXPLODE:' => array(
				'symbol_lexeme' => ':EXPLODE:',
				'symbol_token' => array('/^\[\:EXPLODE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'explode.gif" alt="Explode">'),
				'group' => 'smiley',
			),
			':BOP:' => array(
				'symbol_lexeme' => ':BOP:',
				'symbol_token' => array('/^\[\:BOP\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'bop.gif" alt="Bop">'),
				'group' => 'smiley',
			),
			':WHAT:' => array(
				'symbol_lexeme' => ':WHAT:',
				'symbol_token' => array('/^\[\:WHAT\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'what.gif" alt="What">'),
				'group' => 'smiley',
			),
			':DUNCE:' => array(
				'symbol_lexeme' => ':DUNCE:',
				'symbol_token' => array('/^\[\:DUNCE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'dunce.gif" alt="Dunce">'),
				'group' => 'smiley',
			),
			':DROOL:' => array(
				'symbol_lexeme' => ':DROOL:',
				'symbol_token' => array('/^\[\:DROOL\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'drool.gif" alt="Drool">'),
				'group' => 'smiley',
			),
			':EXCITED:' => array(
				'symbol_lexeme' => ':EXCITED:',
				'symbol_token' => array('/^\[\:EXCITED\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'excited.gif" alt="Excited">'),
				'group' => 'smiley',
			),
			':HUGLOVE:' => array(
				'symbol_lexeme' => ':HUGLOVE:',
				'symbol_token' => array('/^\[\:HUGLOVE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'huglove.gif" alt="Hug love">'),
				'group' => 'smiley',
			),
			':CUPID:' => array(
				'symbol_lexeme' => ':CUPID:',
				'symbol_token' => array('/^\[\:CUPID\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'cupid.gif" alt="Cupid">'),
				'group' => 'smiley',
			),
			':WIGGLE:' => array(
				'symbol_lexeme' => ':WIGGLE:',
				'symbol_token' => array('/^\[\:WIGGLE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'wiggle.gif" alt="Wiggle">'),
				'group' => 'smiley',
			),
			':CRUTCHES:' => array(
				'symbol_lexeme' => ':CRUTCHES:',
				'symbol_token' => array('/^\[\:CRUTCHES\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'crutches.gif" alt="Crutches">'),
				'group' => 'smiley',
			),
			':SADANGEL:' => array(
				'symbol_lexeme' => ':SADANGEL:',
				'symbol_token' => array('/^\[\:SADANGEL\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'sadangel.gif" alt="Sad angel">'),
				'group' => 'smiley',
			),
			':PCWHACK:' => array(
				'symbol_lexeme' => ':PCWHACK:',
				'symbol_token' => array('/^\[\:PCWHACK\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'pcwhack.gif" alt="PC whack">'),
				'group' => 'smiley',
			),
			':FART:' => array(
				'symbol_lexeme' => ':FART:',
				'symbol_token' => array('/^\[\:FART\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'fart.gif" alt="Fart">'),
				'group' => 'smiley',
			),
			':JUMPY:' => array(
				'symbol_lexeme' => ':JUMPY:',
				'symbol_token' => array('/^\[\:JUMPY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'jumpy.gif" alt="Jumpy">'),
				'group' => 'smiley',
			),
			':SLAPHEAD:' => array(
				'symbol_lexeme' => ':SLAPHEAD:',
				'symbol_token' => array('/^\[\:SLAPHEAD\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'slaphead.gif" alt="Slap head">'),
				'group' => 'smiley',
			),
			':KNIGHT:' => array(
				'symbol_lexeme' => ':KNIGHT:',
				'symbol_token' => array('/^\[\:KNIGHT\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'knight.gif" alt="Knight">'),
				'group' => 'smiley',
			),
			':WORTHY:' => array(
				'symbol_lexeme' => ':WORTHY:',
				'symbol_token' => array('/^\[\:WORTHY\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'worthy.gif" alt="Worthy">'),
				'group' => 'smiley',
			),
			':DRINKS:' => array(
				'symbol_lexeme' => ':DRINKS:',
				'symbol_token' => array('/^\[\:DRINKS\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'drinks.gif" alt="Drinks">'),
				'group' => 'smiley',
			),
			':CLOUD9:' => array(
				'symbol_lexeme' => ':CLOUD9:',
				'symbol_token' => array('/^\[\:CLOUD9\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'cloud9.gif" alt="Cloud 9">'),
				'group' => 'smiley',
			),
			':TOMATOES:' => array(
				'symbol_lexeme' => ':TOMATOES:',
				'symbol_token' => array('/^\[\:TOMATOES\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'tomatoes.gif" alt="Tomatoes">'),
				'group' => 'smiley',
			),
			':STRETCHER:' => array(
				'symbol_lexeme' => ':STRETCHER:',
				'symbol_token' => array('/^\[\:STRETCHER\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'stretcher.gif" alt="Stretcher">'),
				'group' => 'smiley',
			),
			':BALOON:' => array(
				'symbol_lexeme' => ':BALOON:',
				'symbol_token' => array('/^\[\:BALOON\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'baloon.gif" alt="Baloon">'),
				'group' => 'smiley',
			),
			':WAVE:' => array(
				'symbol_lexeme' => ':WAVE:',
				'symbol_token' => array('/^\[\:WAVE\:\]$/'),
				'symbol_html' => array('<img src="' . $smileys . 'wave.gif" alt="Wave">'),
				'group' => 'smiley',
			),

			
			//
			// Double width emoticons
			//
//			':BYE:' => array(
//				'symbol_lexeme' => ':BYE:',
//				'symbol_token' => array('/^\[\:BYE\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'bye.gif" alt="Bye">'),
//				'group' => 'smiley',
//			),
//			':SLEEP:' => array(
//				'symbol_lexeme' => ':SLEEP:',
//				'symbol_token' => array('/^\[\:SLEEP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'sleep.gif" alt="Sleep">'),
//				'group' => 'smiley',
//			),
//			':THUMBSUP:' => array(
//				'symbol_lexeme' => ':THUMBSUP:',
//				'symbol_token' => array('/^\[\:THUMBSUP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'thumbsup.gif" alt="Thumbs up">'),
//				'group' => 'smiley',
//			),
//			':HUG:' => array(
//				'symbol_lexeme' => ':HUG:',
//				'symbol_token' => array('/^\[\:HUG\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'hug.gif" alt="Hug">'),
//				'group' => 'smiley',
//			),
//			':CHEEKKISS:' => array(
//				'symbol_lexeme' => ':CHEEKKISS:',
//				'symbol_token' => array('/^\[\:CHEEKKISS\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'cheekkiss.gif" alt="Cheekkiss">'),
//				'group' => 'smiley',
//			),
//			':POKE:' => array(
//				'symbol_lexeme' => ':POKE:',
//				'symbol_token' => array('/^\[\:POKE\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'poke.gif" alt="Poke">'),
//				'group' => 'smiley',
//			),
//			':ROFL:' => array(
//				'symbol_lexeme' => ':ROFL:',
//				'symbol_token' => array('/^\[\:ROFL\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'rofl.gif" alt="ROFL">'),
//				'group' => 'smiley',
//			),
//			':THERETHERE:' => array(
//				'symbol_lexeme' => ':THERETHERE:',
//				'symbol_token' => array('/^\[\:THERETHERE\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'therethere.gif" alt="There-there">'),
//				'group' => 'smiley',
//			),
//			':WHIP:' => array(
//				'symbol_lexeme' => ':WHIP:',
//				'symbol_token' => array('/^\[\:WHIP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'whip.gif" alt="Whip">'),
//				'group' => 'smiley',
//			),
//			':ARMS:' => array(
//				'symbol_lexeme' => ':ARMS:',
//				'symbol_token' => array('/^\[\:ARMS\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'arms.gif" alt="Arms">'),
//				'group' => 'smiley',
//			),
//
//			':OUCH:' => array(
//				'symbol_lexeme' => ':OUCH:',
//				'symbol_token' => array('/^\[\:OUCH\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'ouch.gif" alt="Ouch">'),
//				'group' => 'smiley',
//			),
//			':STEREO:' => array(
//				'symbol_lexeme' => ':STEREO:',
//				'symbol_token' => array('/^\[\:STEREO\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'stereo.gif" alt="Stereo">'),
//				'group' => 'smiley',
//			),
//
////			':SEX:' =>array(
////				'symbol_lexeme' => ':SEX:',
////				'symbol_token' => array('/^\[\:SEX\:\]$/'),
////				'symbol_html' => array('<img src="' . $smileys . 'sex.gif" alt="Sex">'),
////				'group' => 'smiley',
////			),
//			':CHAPLIN:' => array(
//				'symbol_lexeme' => ':CHAPLIN:',
//				'symbol_token' => array('/^\[\:CHAPLIN\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'chaplin.gif" alt="Chaplin">'),
//				'group' => 'smiley',
//			),
//			':POPCORN:' => array(
//				'symbol_lexeme' => ':POPCORN:',
//				'symbol_token' => array('/^\[\:POPCORN\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'popcorn.gif" alt="Popcorn">'),
//				'group' => 'smiley',
//			),
//			':STAMP:' => array(
//				'symbol_lexeme' => ':STAMP:',
//				'symbol_token' => array('/^\[\:STAMP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'stamp.gif" alt="Stamp">'),
//				'group' => 'smiley',
//			),
//			':BOUNCY:' => array(
//				'symbol_lexeme' => ':BOUNCY:',
//				'symbol_token' => array('/^\[\:BOUNCY\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'bouncy.gif" alt="Bouncy">'),
//				'group' => 'smiley',
//			),
//			':RIP:' => array(
//				'symbol_lexeme' => ':RIP:',
//				'symbol_token' => array('/^\[\:RIP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'rip.gif" alt="R.I.P">'),
//				'group' => 'smiley',
//			),
//
//			':BANNED:' => array(
//				'symbol_lexeme' => ':BANNED:',
//				'symbol_token' => array('/^\[\:BANNED\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'banned.gif" alt="Banned">'),
//				'group' => 'smiley',
//			),
//			':FLOWERS:' => array(
//				'symbol_lexeme' => ':FLOWERS:',
//				'symbol_token' => array('/^\[\:FLOWERS\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'flowers.gif" alt="Flowers">'),
//				'group' => 'smiley',
//			),
//			':JAWDROP:' => array(
//				'symbol_lexeme' => ':JAWDROP:',
//				'symbol_token' => array('/^\[\:JAWDROP\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'jawdrop.gif" alt="Jaw drop">'),
//				'group' => 'smiley',
//			),
//			':WELCOME:' => array(
//				'symbol_lexeme' => ':WELCOME:',
//				'symbol_token' => array('/^\[\:WELCOME\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'welcome.gif" alt="Welcome">'),
//				'group' => 'smiley',
//			),
//			':PIRATE:' => array(
//				'symbol_lexeme' => ':PIRATE:',
//				'symbol_token' => array('/^\[\:PIRATE\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'pirate.gif" alt="Pirate">'),
//				'group' => 'smiley',
//			),
//			':PUKE:' => array(
//				'symbol_lexeme' => ':PUKE:',
//				'symbol_token' => array('/^\[\:PUKE\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'puke.gif" alt="Puke">'),
//				'group' => 'smiley',
//			),
//			':BADDAY:' => array(
//				'symbol_lexeme' => ':BADDAY:',
//				'symbol_token' => array('/^\[\:BADDAY\:\]$/'),
//				'symbol_html' => array('<img src="' . $smileys . 'badday.gif" alt="Bad day">'),
//				'group' => 'smiley',
//			),
////			':ATOMIC:' => array(
////				'symbol_lexeme' => ':ATOMIC:',
////				'symbol_token' => array('/^\[\:ATOMIC\:\]$/'),
////				'symbol_html' => array('<img src="' . $smileys . 'atomic.gif" alt="Atomic">'),
////				'group' => 'smiley',
////			),

		);
		
		foreach($lexemes as $key => &$lexeme)
		{
			// Count the tokens
			$lexeme['token_count'] = count($lexeme['symbol_token']);
			
			if ($lexeme['token_count'] > 1)
			{
				$lexeme['allowed_nestable'] = array();

				if ( ! array_key_exists('black_list', $lexeme)) {
					$lexeme['black_list'] = array();
				}
				if ( ! array_key_exists('groups', $lexeme['black_list'])) {
					$lexeme['black_list']['groups'] = array();		
				}
				if ( ! array_key_exists('tags', $lexeme['black_list'])) {
					$lexeme['black_list']['tags'] = array();
				}
				if ( ! array_key_exists('white_list', $lexeme)) {
					$lexeme['white_list'] = array();
				}
				if ( ! array_key_exists('groups', $lexeme['white_list'])) {
					$lexeme['white_list']['groups'] = array();			
				}
				if ( ! array_key_exists('tags', $lexeme['white_list'])) {
					$lexeme['white_list']['tags'] = array();
				}
			
				// Compile the list of 'allowed_nestable'
				foreach($lexemes as $nestable)
				{
					// 
					// By default all tags can have nested content.
					// If a black list is defined, everything on the black-list is prevented from being nested.
					// If a white list is defined, groups on the white-list will override the black-list except individual tags.
					// To override a blacklisted group for a single tag, white list the tag. 
					//
					if (((! in_array($nestable['group'], $lexeme['black_list']['groups'])
					&&	 ! in_array('*', $lexeme['black_list']['groups'])
					&&	 ! in_array('*', $lexeme['black_list']['tags']))
					|| (   in_array($nestable['group'], $lexeme['white_list']['groups'])
					||	   in_array($nestable['symbol_lexeme'], $lexeme['white_list']['tags'])
					||	   in_array('*', $lexeme['white_list']['groups'])
					||	   in_array('*', $lexeme['white_list']['tags'])))
					&&	 ! in_array($nestable['symbol_lexeme'], $lexeme['black_list']['tags']))
					{
						$lexeme['allowed_nestable'][] = $nestable['symbol_lexeme'];				
					}
				}
			}
		}
				
		$this->lexemes = $lexemes;
	}
	
}
