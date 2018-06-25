(function ( $, mw ) {

	/**
	 *
	 * @constructor
	 */
	function TrainingBlock( element ) {
		this._$element = $( element );
		this._$checkBox = null;
		this._title = '';
		this._block_id = null;
		this._init();
		this._load();
		this._bind();
	}

	/**
	 *
	 */
	TrainingBlock.prototype._init = function () {

		var title = this._$element.data( 'title' );
		if ( title ) {
			this._title = title;
		}

		this._block_id = this._$element.data( 'block-id' );

		this._$checkBox = this._$element.find( 'input[type="checkbox"]' );

	};

	TrainingBlock.prototype._load = function () {

		var api = new mw.Api();
		var self = this;

		api.get( {
			action: 'radianttraining',
			do: 'fetch',
			page_id: mw.config.get( 'wgArticleId' ),
			block_id: this._block_id
		} ).done( function ( data ) {

			var resp = data.radianttraining;
			if ( resp.status ) {
				var completed = resp.is_completed;
				if ( completed ) {
					self._$checkBox.prop( 'checked', true );
				}
				if( resp.is_allowed ) {
					self._$checkBox.prop( 'disabled', false );
				}
				self._$element.removeClass( 'training-block--loading' );
			}

		} );

	};

	TrainingBlock.prototype._bind = function () {
		this._$checkBox.change( this._onChange.bind( this ) );
	};

	TrainingBlock.prototype._onChange = function () {
		var checked = this._$checkBox.is( ':checked' );
		var api = new mw.Api();
		api.post( {
			action: 'radianttraining',
			do: checked ? 'complete' : 'remove',
			page_id: mw.config.get( 'wgArticleId' ),
			block_id: this._block_id
		} ).done( function ( data ) {
			console.log( data );
		} );
	};

	mw.trainingBlock = TrainingBlock;

})( jQuery, mediaWiki );
