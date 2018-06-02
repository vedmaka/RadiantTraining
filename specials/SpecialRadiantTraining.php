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

    /**
     * Show the page to the user
     *
     * @param string $sub The subpage string argument (if any).
     *  [[Special:RadiantTraining/subpage]].
     */
    public function execute( $sub )
    {
        $out = $this->getOutput();

        $out->setPageTitle( $this->msg( 'radianttraining-helloworld' ) );

        $out->addHelpLink( 'How to become a MediaWiki hacker' );

        $out->addWikiMsg( 'radianttraining-helloworld-intro' );
    }

    protected function getGroupName()
    {
        return 'other';
    }
}
