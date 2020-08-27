<?php

namespace MediaWiki\Extension\Accordion;

use Html;
use Parser;
use PPFrame;
use Sanitizer;

/**
 * Class that generates the accordion HTML
 *
 * @license MIT
 */
class Accordion {

	/**
	 * @var int Sequential to give a more unique id
	 */
	private $sequential = 0;

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param ?string $text Raw, untrimmed wikitext content of the <accordion> tag, if any
	 * @param string[] $argv Arguments as given in <accordion>, already trimmed
	 *
	 * @return string HTML
	 */
	public function render(
		Parser $parser,
		PPFrame $frame,
		?string $text,
		array $argv
	) : string {

		$this->sequential++;
		$parserOutput = $parser->getOutput();
		$parserOutput->addModuleStyles( 'ext.accordion.styles' );

		$key = sprintf( 'accordion_%d_%s', $this->sequential, md5( $text ) );
		$sections = preg_split( '/^\\|-\\|/m', $text );
		$classes = [ 'mw-ext-accordion' ];
		if ( array_key_exists( 'class', $argv ) ) {
			$classes[] = $argv['class'];
		}
		$attrs = [
			'id' => $key,
			'class' => implode( ' ', $classes )
		];
		if ( array_key_exists( 'style', $argv ) ) {
			$attrs['style'] = Sanitizer::checkCss( $argv['style'] );
		}
		$activesection = 1;
		if (
			array_key_exists( 'activesection', $argv ) &&
			preg_match( '/^\\d{1,2}$/', $argv['activesection'] )
		) {
			$activesection = intval( $argv['activesection'], 10 );
			if ( $activesection < 0 || $activesection > count( $sections ) ) {
				$activesection = 1;
			}
		}

		$html = '';
		for ( $i = 1; $i <= count( $sections ); $i++ ) {
			$active = ( $activesection == $i );
			$html .= $this->renderSection(
				$sections[$i - 1],
				$parser,
				$frame,
				$key,
				$i,
				$active
			);
		}

		$html = Html::rawElement( 'div', $attrs, $html );

		return $html;
	}

	/**
	 * Renders a section of the accordion
	 *
	 * @param $section string Section contents
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param $key string Current accordion key
	 * @param $sectionNumber int section number
	 * @param $active bool Tells if section is active
	 *
	 * @return string HTML
	 */
	private function renderSection(
		string $section,
		Parser $parser,
		PPFrame $frame,
		string $key,
		int $sectionNumber,
		bool $active
	) : string {
		if ( empty( trim( $section ) ) ) {
			return '';
		}
		$parts = explode( '=', $section, 2 );
		$heading = trim( $parts[0] );
		$body = isset( $parts[1] ) ? trim( $parts[1] ) : '';
		if ( $heading === '' ) {
			return '';
		}

		$id = sprintf( '%s_%d', $key, $sectionNumber );
		$bodyHTML = $parser->recursiveTagParse( $body, $frame );

		$radio = Html::radio( $key, $active, [
			'id' => $id
		] );
		// Content is escaped
		$label = Html::label( $heading, $id );
		$content = Html::rawElement( 'div', [], $bodyHTML );

		return Html::rawElement(
			'div',
			[
				'class' => 'accordion-section'
			],
			$radio . $label . $content
		);
	}
}
