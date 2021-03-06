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
		$parser->setFunctionTagHook( 'training_status', 'RadiantTraining::render_training_status', SFH_OBJECT_ARGS );
	}

	/**
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addExtensionTable( 'training_blocks', dirname( __FILE__ ) . '/schema/training_blocks.sql' );
		$updater->addExtensionTable( 'training_records', dirname( __FILE__ ) . '/schema/training_records.sql' );
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

		if ( $content->getModel() == CONTENT_MODEL_WIKITEXT ) {
			RadiantTraining::getInstance()->updateModulesOnPage( $wikiPage->getTitle()
			                                                              ->getArticleID(), $content->getWikitextForTransclusion() );
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
	public static function onTitleMoveComplete(
		&$title, &$newTitle, $user, $oldid, $newid, $reason, $revision
	) {

		// RadiantTraining::getInstance()->moveModules( $oldid, $newTitle->getArticleID() );

	}

	/**
	 * @param Article $article
	 * @param bool $outputDone
	 * @param bool $pcache
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {

		$out = $article->getContext()->getOutput();
		if( RadiantTraining::getInstance()->hasTrainings( $article->getId() ) ) {
			if( $out->getUser()->isAllowed('do-training') ) {
				$out->enableClientCache(false);
				$span = Html::rawElement( 'button', array(), 'Mark whole page as completed' );
				$html = Html::rawElement( 'div', array( 'class' => 'training--header-control' ), $span );
				$out->addHTML( $html );
			}
		}

	}

}
