<?php

/**
 * Class TrainingModel
 */
class TrainingModel implements ITrainingModel {

	private $dbr;
	private $dbw;
	private $fields;

	/**
	 * TrainingModel constructor.
	 */
	public function __construct() {
		$this->dbr = wfGetDB( DB_SLAVE );
		$this->dbw = wfGetDB( DB_MASTER );
		$this->fields = array();
	}

	/**
	 * @param $id
	 *
	 * @return null|self
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function findById( $id ) {
		$entry = new static();
		$entry->id = $id;

		if ( $entry->load() ) {
			return $entry;
		}

		return null;
	}

	/**
	 * @return bool
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	private function load() {
		if ( !array_key_exists( 'id', $this->fields ) ) {
			return false;
		}

		$result = $this->dbr->select( static::getTable(), '*', array(
				'id' => $this->fields['id']
			) );

		if ( !$result || !$result->numRows() ) {
			return false;
		}

		$entry = $result->fetchRow();
		foreach ( $entry as $key => $value ) {
			if ( !is_string( $key ) ) {
				continue;
			}
			$this->fields[$key] = $value;
		}

		return true;

	}

	protected static function getTable() {
		// Returns table name
	}

	/**
	 * @param null $condition
	 *
	 * @return null|TrainingModel[]
	 */
	public static function findAll( $condition = null ) {
		$entries = array();
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( static::getTable(), 'id', $condition );
		if ( !$result || !$result->numRows() ) {
			return null;
		}
		foreach ( $result as $item ) {
			$entry = new static();
			$entry->id = $item->id;
			if ( $entry->load() ) {
				$entries[] = $entry;
			}
		}
		return $entries;
	}

	public static function countAll( $condition = null ) {
		$db = wfGetDB(DB_SLAVE);
		$ret = $db->selectRowCount( static::getTable(), 'id', $condition );

		return $ret;
	}

	/**
	 * @param $condition
	 *
	 * @return null|TrainingModel
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public static function findBy( $condition ) {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( static::getTable(), 'id', $condition, __METHOD__, array(
				'LIMIT' => 1
			) );

		if ( !$result || !$result->numRows() ) {
			return null;
		}

		$item = $result->fetchRow();
		$entry = new static();
		$entry->id = $item['id'];

		if ( $entry->load() ) {
			return $entry;
		}

		return null;
	}

	/**
	 * @return integer|null
	 */
	public function getId() {
		return array_key_exists( 'id', $this->fields ) ? $this->fields['id'] : null;
	}

	/**
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 * @throws Exception
	 */
	public function save() {
		$values = $this->fields;

		$values['updated_at'] = time();
		if ( !array_key_exists( 'id', $this->fields ) ) {
			$values['created_at'] = time();
		}

		$this->dbw->upsert( static::getTable(), $values, array( 'id' ), $values );

		if ( !array_key_exists( 'id', $this->fields ) ) {
			$this->fields['id'] = $this->dbw->insertId();
		}
	}

	/**
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function delete() {
		if ( !array_key_exists( 'id', $this->fields ) ) {
			return;
		}

		$this->dbw->delete( static::getTable(), array(
				'id' => $this->fields['id']
			) );
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {
		return array_key_exists( $name, $this->fields ) ? $this->fields[$name] : null;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set( $name, $value ) {
		$this->fields[$name] = $value;
	}

}
