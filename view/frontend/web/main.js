// 2016-09-28
define ([
	'Df_Payment/custom'
	,'Dfe_Square/API'
], function(parent, Square) {'use strict'; return parent.extend({
	defaults: {
		df: {
			// 2016-09-28
			// @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
			formTemplate: 'Dfe_Square/form'
		}
	},
	/**
	 * 2016-09-28
	 * @return {Object}
	*/
	initialize: function() {
		this._super();
		this.config('applicationID');
		return this;
	},
	/**
	 * 2016-09-28
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L127-L159
	 * @used-by https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	 * @param {this} _this
	*/
	placeOrder: function(_this) {
		if (this.validate()) {
		}
	}
});});
