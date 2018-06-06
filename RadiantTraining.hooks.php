<?php

/**
 * Hooks for RadiantTraining extension
 *
 * @file
 * @ingroup Extensions
 */
class RadiantTrainingHooks {

	public static function onExtensionLoad() {

	}

	/**
	 * @param Parser $parser
	 *
	 * @throws MWException
	 */
	public static function onParserFirstCallInit( $parser ) {
		$parser->setFunctionTagHook( 'training', 'RadiantTraining::renderBlock', SFH_OBJECT_ARGS );
	}

	/**
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addExtensionTable( 'training_blocks', dirname( __FILE__ ) . '/schema/training_blocks.sql' );
	}

	/**
	 * @param WikiPage        $wikiPage
	 * @param User            $user
	 * @param WikitextContent $content
	 * @param string          $summary
	 * @param bool            $isMinor
	 * @param bool            $isWatch
	 * @param                 $section
	 * @param int             $flags
	 * @param Revision        $revision
	 * @param Status          $status
	 * @param int|null        $baseRevId
	 * @param int|null        $undidRevId
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function onPageContentSaveComplete(
		$wikiPage, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId, $undidRevId
	) {

		if( $content->getModel() == CONTENT_MODEL_WIKITEXT ) {
			RadiantTraining::getInstance()->updateModulesOnPage(
				$wikiPage->getTitle()->getArticleID(), $content->getWikitextForTransclusion() );
		}

	}

	/**
	 * @param Title    $title
	 * @param Title    $newTitle
	 * @param User     $user
	 * @param          $oldid
	 * @param          $newid
	 * @param          $reason
	 * @param Revision $revision
	 *
	 */
	public static function onTitleMoveComplete( &$title, &$newTitle, $user, $oldid, $newid,
		$reason, $revision ) {

		// RadiantTraining::getInstance()->moveModules( $oldid, $newTitle->getArticleID() );

	}

}
