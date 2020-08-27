<?php

namespace MediaWiki\Extension\Accordion;

use Parser;
use PPFrame;

/**
 * Class that implements the hooks called from MediaWiki
 *
 * @license MIT
 */
class Hooks {

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param Parser $parser
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		// Register the hook with the parser
		$parser->setHook( 'accordion', [ __CLASS__, 'renderAccordion' ] );

		// Continue
		return true;
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserClearState
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserCloned
	 *
	 * @param Parser $parser
	 */
	public static function onParserClearStateOrCloned( Parser $parser ) {
		unset( $parser->extAccordion );
	}

	/**
	 * Parser hook for the <accordion> tag.
	 *
	 * @param ?string $text Raw, untrimmed wikitext content of the <accordion> tag, if any
	 * @param string[] $argv
	 * @param Parser $parser
	 * @param PPFrame $frame
	 *
	 * @return string HTML
	 */
	public static function renderAccordion(
		?string $text,
		array $argv,
		Parser $parser,
		PPFrame $frame
	) : string {

		$accordion = self::getInstanceFromParser( $parser );
		$result = $accordion->render( $parser, $frame, $text, $argv );

		return $result;
	}

	/**
	 * Get instance of Accordion from Parser
	 *
	 * @param Parser $parser
	 *
	 * @return Accordion instance
	 */
	private static function getInstanceFromParser( Parser $parser ) : Accordion {
		if ( !isset( $parser->extAccordion ) ) {
			$parser->extAccordion = new Accordion( $parser );
		}
		return $parser->extAccordion;
	}
}
