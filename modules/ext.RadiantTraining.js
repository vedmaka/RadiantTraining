$( function () {

	var $blocks = $( '.training-block' );

	if ( $blocks.length ) {

		$( $blocks ).each( function () {
			new mw.trainingBlock( $( this ) );
		} );

		var $control = $( '.training--header-control' );

		/*var isAllChecked = true;
		$( $blocks ).each( function () {
			var $checkbox = $(this).find('input[type="checkbox"]');
			if( !$checkbox.is(':checked') ) {
				isAllChecked = false;
				return false;
			}
		});*/

		//$control.find( 'input' ).prop( 'checked', isAllChecked );

		if ( $control.length ) {
			$control.find('button').on( 'click', function ( e ) {
				selectAllTrainings();
			} );
		}

		function selectAllTrainings() {
			$( $blocks ).each( function () {
				var $checkbox = $( this ).find( 'input[type="checkbox"]' );
				if ( !$checkbox.is( ':checked' ) ) {
					$checkbox.click();
				}
			} );
		}

		/*function deselectAllTrainings() {
			$( $blocks ).each( function () {
				var $checkbox = $( this ).find( 'input[type="checkbox"]' );
				if ( $checkbox.is( ':checked' ) ) {
					$checkbox.click();
				}
			} );
		}*/

	}

} );
