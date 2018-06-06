<?php

/**
 * RadiantTraining SpecialPage for RadiantTraining extension
 *
 * @file
 * @ingroup Extensions
 */
class SpecialRadiantTraining extends SpecialPage
{
    public function __construct()
    {
        parent::__construct( 'RadiantTraining' );
    }

    public function execute( $sub )
    {
        $out = $this->getOutput();

    }

    protected function getGroupName()
    {
        return 'other';
    }
}
