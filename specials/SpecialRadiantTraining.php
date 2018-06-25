<?php

use MediaWiki\MediaWikiServices;

/**
 * RadiantTraining SpecialPage for RadiantTraining extension
 *
 * @file
 * @ingroup Extensions
 */
class SpecialRadiantTraining extends SpecialPage {
	public function __construct() {
		parent::__construct( 'RadiantTraining' );
	}

	/**
	 * @param null|string $sub
	 * @throws PermissionsError
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();
		$out->setPageTitle('Training summary');

		if ( !$this->getUser()->isAllowed( 'manage-training' ) ) {
			$this->displayRestrictionError();
		}
		$out->addModules( 'ext.radianttraining.special' );

		$data = array(
			'users' => array()
		);

		$total = TrainingBlockModel::countAll();
		$db = wfGetDB(DB_SLAVE);
		$result = $db->select('user', '*');
		while($res = $result->fetchRow()) {
			$user_id = $res['user_id'];
			$item = array();
			$user = User::newFromId($user_id);
			$item['username'] = $user->getName();
			$item['userlink'] = $user->getUserPage()->getFullURL();

			$userRecords = TrainingRecordModel::findAll( array( 'user_id' => $user_id ) );
			$completedByUser = count( $userRecords ); //TrainingRecordModel::countAll( array( 'user_id' => $user_id ) );

			$item['progress'] = $completedByUser .' / '. $total;

			$item['records'] = array();
			foreach ( $userRecords as $record ) {

				$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();

				$blockTitle = 'Untitled';
				$relatedBlock = TrainingBlockModel::findBy( array(
					'block_id' => $record->block_text_id,
					'page_id' => $record->page_id
				) );
				if( $relatedBlock && $relatedBlock->title ) {
					$blockTitle = '<a href="' .
						Title::newFromID( $record->page_id )->getFullURL()
						.'#'.$relatedBlock->block_id . '">' . $relatedBlock->title . '</a>';
				}

				$item['records'][] = array(
					'page' => $linkRenderer->makeLink( Title::newFromID( $record->page_id ) ),
					'title' => $blockTitle
				);
			}

			$data['users'][] = $item;
		}

		$tp = new TemplateParser( dirname( __FILE__ ) . '/../templates', true );
		$html = $tp->processTemplate( 'special', $data );

		$out->addHTML( $html );
	}

	protected function getGroupName() {
		return 'other';
	}
}
