<?php

/**
 * Class TrainingBlockModel
 *
 * @property integer id
 * @property integer page_id
 * @property string  block_id
 * @property string  title
 * @property integer created_at
 * @property integer updated_at
 */
class TrainingBlockModel extends TrainingModel {

	/**
	 * @param $id
	 *
	 * @return null|TrainingBlockModel
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function findById( $id ) {
		return parent::findById( $id );
	}

	/**
	 * @param null $condition
	 *
	 * @return null|TrainingBlockModel[]
	 */
	public static function findAll( $condition = null ) {
		return parent::findAll( $condition ); // TODO: Change the autogenerated stub
	}

	/**
	 * @param array $condition
	 *
	 * @return null|TrainingBlockModel
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function findBy( $condition ) {
		return parent::findBy( $condition ); // TODO: Change the autogenerated stub
	}

	protected static function getTable() {
		return 'training_blocks';
	}

}
