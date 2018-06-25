<?php

/**
 * Singleton class for RadiantTraining extension
 *
 * @file
 * @ingroup Extensions
 */
class RadiantTraining {

	private static $instance = null;

	/**
	 * Singleton getter
	 *
	 * @return RadiantTraining
	 */
	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @param Parser  $parser
	 * @param PPFrame $frame
	 * @param string  $content
	 * @param array   $params
	 *
	 * @return array|string
	 */
	public static function renderBlock( &$parser, $frame, $content = '', $params = array() ) {

		if ( !array_key_exists( 'id', $params ) ) {
			return 'Error: ID parameter is not specified for training block tag!';
		}

		$parser->getOutput()->addModuleStyles( 'ext.radianttraining.styles' );
		$parser->getOutput()->addModules( 'ext.radianttraining.main' );

		$inner = Html::openElement( 'label' );
		$inner .= Html::input( null, '', 'checkbox', array( 'disabled' ) );
		if ( isset( $params['title'] ) ) {
			$inner .= Html::element( 'span', array(), $params['title'] );
		}
		$inner .= Html::closeElement( 'label' );

		$html = Html::rawElement( 'div', array(
			'class' => 'training-block training-block--loading',
			'data-title' => isset( $params['title'] ) ? trim( str_replace( '"', '', $params['title'] ) ) : '',
			'data-block-id' => trim( str_replace( '"', '', $params['id'] ) )
		), $inner );

		return array(
			$html,
			'markerType' => 'nowiki'
		);
	}

	/**
	 * @param int    $page_id
	 * @param string $text
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function updateModulesOnPage( $page_id, $text ) {
		$modules = $this->extractTrainingModulesFromText( $text );
		if ( count( $modules ) ) {
			$this->invalidateModules( $page_id, $modules );
		} else {
			$this->wipeModules( $page_id );
		}
	}

	private function extractTrainingModulesFromText( $text ) {
		$modules = array();
		$exp = '/\<training\s([^\/\>]+)\/?\>/';
		$title_exp = "/title\s?\=\s?[\\\"\']([^\\\"\']+)\\\"/";
		$id_exp = "/id\s?\=\s?[\\\"\']([^\\\"\']+)\\\"/";
		$matches = array();
		if ( preg_match_all( $exp, $text, $matches ) ) {
			foreach ( $matches[1] as $match ) {
				$field_matches = array();
				$title = '';
				if ( preg_match( $title_exp, $match, $field_matches ) ) {
					$title = $field_matches[1];
				}
				$field_matches = array();
				$id = null;
				if ( preg_match( $id_exp, $match, $field_matches ) ) {
					$id = $field_matches[1];
				}

				if ( empty( $id ) ) {
					continue;
				}

				$modules[$id] = array(
					'id' => $id,
					'title' => $title
				);

			}
		}
		return $modules;
	}

	/**
	 * Compare newly added modules against existing ones and makes them match each together
	 *
	 * @param       $page_id
	 *
	 * @param array $rawModules
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function invalidateModules( $page_id, $rawModules ) {
		$existingModules = TrainingBlockModel::findAll( array( 'page_id' => $page_id ) );

		$existingModulesIds = array();
		$rawModulesIds = array();

		if ( $existingModules ) {
			foreach ( $existingModules as $exisingModule ) {
				$existingModulesIds[$exisingModule->block_id] = $exisingModule->block_id;
			}
		}

		foreach ( $rawModules as $rawModule ) {
			$rawModulesIds[] = $rawModule['id'];
		}

		// IDs of existing modules missing in new modules array
		$diffIds = array_diff( $existingModulesIds, $rawModulesIds );

		foreach ( $diffIds as $diffId ) {
			$module = TrainingBlockModel::findBy( array( 'block_id' => $diffId, 'page_id' => $page_id ) );
			if ( $module ) {
				$module->delete();
				// Remove module ID from existing modules array
				unset( $existingModulesIds[$diffId] );
			}
		}

		// IDs of existing modules coming from new modules array
		$intersectIds = array_intersect( $existingModulesIds, $rawModulesIds );

		foreach ( $intersectIds as $intersectId ) {
			$module = TrainingBlockModel::findBy( array( 'block_id' => $intersectId, 'page_id' => $page_id ) );
			if ( $module ) {
				$rawModuleTitle = $rawModules[$intersectId]['title'];
				// Update module params in database using block_id as key
				if ( $rawModuleTitle != $module->title ) {
					$module->title = $rawModuleTitle;
					$module->save();
				}
			}
		}

		// Brand new modules
		$newIds = array_diff( $rawModulesIds, $existingModulesIds );

		foreach ( $newIds as $newId ) {
			$module = new TrainingBlockModel();
			$module->title = $rawModules[$newId]['title'];
			$module->block_id = $rawModules[$newId]['id'];
			$module->page_id = $page_id;
			$module->save();
		}

	}

	/**
	 * @param int $page_id
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function wipeModules( $page_id ) {
		$modules = TrainingBlockModel::findAll( array(
			'page_id' => $page_id
		) );
		if ( $modules ) {
			foreach ( $modules as $module ) {
				$module->delete();
			}
		}
	}

	/**
	 * @param int $oldid
	 * @param int $newid
	 *
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function moveModules( $oldid, $newid ) {
		$modules = TrainingBlockModel::findAll( array( 'page_id' => $oldid ) );
		if ( $modules ) {
			foreach ( $modules as $module ) {
				$module->page_id = $newid;
				$module->save();
			}
		}
	}

	/**
	 * @param $page_id
	 * @return bool
	 * @throws \Wikimedia\Rdbms\DBUnexpectedError
	 */
	public function hasTrainings( $page_id ) {
		$modules = TrainingBlockModel::findBy( array( 'page_id' => $page_id ) );
		if( $modules ) {
			return true;
		}
		return false;
	}

}
