( function( $, mw ) {

	function TrainingStat($element) {
		this._$element = $($element);
		this._pageId = null;
		this._api = null;
		this._checkCount = 0;
		this._checkTotal = 0;
		this._init();
	}

	TrainingStat.prototype._init = function() {
		this._pageId = this._$element.data('page');
		this._api = new mw.Api();
		this._load();
	};

	TrainingStat.prototype._load = function() {
		var self = this;
		this._api.get({
			action: 'radianttraining',
			do: 'stat',
			page_id: this._pageId
		}).done(function(data){
			self._checkCount = data.radianttraining.result.count;
			self._checkTotal = data.radianttraining.result.total;
			self._render();
		});
	};

	TrainingStat.prototype._render = function() {

		if( this._checkCount === 0 ) {
			this._$element.text('Not done');
			this._$element.addClass('status-not-done');
		}else{
			if( this._checkCount === this._checkTotal ) {
				this._$element.text('Done');
				this._$element.addClass('status-done');
			}else{
				this._$element.text('Partially done');
				this._$element.addClass('status-partially-done');
			}
		}

	};

	mw.trainingstat = TrainingStat;

})( jQuery, mediaWiki );

$(function(){
	var items = $('.training--training-status');
	if( items.length ) {
		$(items).each(function(i,v){
			new mw.trainingstat(v);
		});
	}
});
