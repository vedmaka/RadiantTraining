<?php

interface ITrainingModel {

	public static function findById( $id );

	public static function findBy( $condition );

	public static function findAll( $condition = null );

	public function getId();

	public function save();

	public function delete();

}
