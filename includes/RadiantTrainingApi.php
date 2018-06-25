<?php

/**
 * Class RadiantTrainingApi
 */
class RadiantTrainingApi extends ApiBase {

	private $formattedData = array();
	private $parsedParams = array();

	/**
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function execute() {

		$this->parsedParams = $this->extractRequestParams( false );
		$this->formattedData['status'] = 0;

		switch ( $this->parsedParams['do'] ) {
			case 'fetch':
				$this->doFetch();
				break;
			case 'complete':
				$this->doComplete();
				break;
			case 'remove':
				$this->doRemove();
				break;
		}

		$this->getResult()->addValue( null, $this->getModuleName(), $this->formattedData );

	}

	/**
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	private function doFetch() {
		$page_id = $this->parsedParams['page_id'];
		$block_id = $this->parsedParams['block_id'];
		$user_id = $this->getUser()->getId();

		$record = TrainingRecordModel::findBy( array(
			'user_id' => $user_id,
			'block_text_id' => $block_id,
			'page_id' => $page_id
		) );

		$this->formattedData['is_completed'] = 0;
		if ( $record ) {
			$this->formattedData['is_completed'] = 1;
		}

		$this->formattedData['is_allowed'] = $this->getUser()->isAllowed('do-training') ? 1 : 0;

		$this->formattedData['status'] = 1;

	}

	/**
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 * @throws ApiUsageException
	 */
	private function doComplete() {

		$this->mustBePosted();

		if( !$this->getUser()->isAllowed('do-training') ) {
			$this->dieWithError('');
		}

		$page_id = $this->parsedParams['page_id'];
		$block_id = $this->parsedParams['block_id'];
		$user_id = $this->getUser()->getId();

		$record = TrainingRecordModel::findBy( array(
			'user_id' => $user_id,
			'block_text_id' => $block_id,
			'page_id' => $page_id
		) );
		if ( $record !== null ) {
			return;
		}

		$record = new TrainingRecordModel();
		$record->user_id = $user_id;
		$record->block_text_id = $block_id;
		$record->page_id = $page_id;
		$record->save();

	}

	/**
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 * @throws ApiUsageException
	 */
	private function doRemove() {

		$this->mustBePosted();

		if( !$this->getUser()->isAllowed('do-training') ) {
			$this->dieWithError('');
		}

		$page_id = $this->parsedParams['page_id'];
		$block_id = $this->parsedParams['block_id'];
		$user_id = $this->getUser()->getId();

		$record = TrainingRecordModel::findBy( array(
			'user_id' => $user_id,
			'block_text_id' => $block_id,
			'page_id' => $page_id
		) );
		if ( !$record ) {
			return;
		}

		$record->delete();

	}

	/**
	 * @return array
	 */
	protected function getAllowedParams() {
		return array(
			'do' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'string'
			),
			'page_id' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_TYPE => 'integer'
			),
			'block_id' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_TYPE => 'string'
			),
		);
	}


}
