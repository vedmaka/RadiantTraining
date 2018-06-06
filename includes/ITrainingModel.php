<?php

interface ITrainingModel {

	public function getId();
	public static function findById($id);
	public static function findBy( $condition );
	public static function findAll($condition = null);
	public function save();
	public function delete();

}
