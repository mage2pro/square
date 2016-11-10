// 2016-09-28
define ([
	'df'
	,'Df_Checkout/js/data'
	,'Df_Payment/card'
	,'Dfe_Square/API'
	,'jquery'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
], function(df, dfc, parent, API, $, creditCardData) {'use strict'; return parent.extend({
	/**
	 * 2016-11-09
	 * The «Square» payment form works only if your checkout page is loaded over HTTPS:
	 * https://mage2.pro/t/2259
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/js/view/payment/mixin.js
	 * @returns {String}
	 */
	debugMessage: df.c(function() {
		return 'https:' === window.location.protocol ? '' : '<span class="df-error">The payment form must be generated on a webpage that uses <b>HTTPS</b>, with one exception: you can test on <b>localhost</b> without using HTTPS: <a href="https://mage2.pro/t/topic/2259" target="_blank" title="Mage2.PRO support forum">https://mage2.pro/t/topic/2259</a></span>'
		;
	}),
	defaults: {
		df: {
			card: {
				expirationComposite: 'expirationComposite'
				// 2016-09-28
				// @used-by mage2pro/core/Payment/view/frontend/web/template/card.html
				,newTemplate: 'Dfe_Square/card/new'
			},
		}
		,expirationComposite: ''
	},
	/**
	 * 2016-09-28
	 * @returns {String[]}
	 */
	dfCardExpirationCompositeId: function() {return this.fid('expiration_composite');},
	/**
	 * 2016-09-28
	 * @returns {String[]}
	 */
	dfCardPostalCodeId: function() {return this.fid('postal_code');},
    /**
	 * 2016-09-28
	 * https://mage2.pro/t/1936
	 */
    dfOnRender: function() {
		var _this = this;
		//var postalCode = dfc.postalCode();
		this.square = new SqPaymentForm({
			applicationId: this.config('applicationID'),
			cardNumber: {elementId: this.dfCardNumberId(),},
			cvv: {elementId: this.dfCardVerificationId(),},
			expirationDate: {elementId: this.dfCardExpirationCompositeId(), placeholder: 'MM/YY'},
			/**
			 * 2016-09-28
			 * Это поле является обязательным.
			 *
			 * 2016-09-29
			 * «This CSS class is assigned to all four of the iframes generated for the payment form.
			 * You can create CSS rules for this class to style the exterior of the inputs
			 * (i.e., borders and margins). See Styling input exteriors for more information.»
			 * https://docs.connect.squareup.com/articles/adding-payment-form/#sqpaymentformparameters
			 */
			inputClass: 'dfe-square',
			/**
			 * 2016-09-29
			 * «Each object in this array defines styles to apply
			 * to the interior of the payment form inputs.
			 * The array can include multiple objects that apply to different ranges of screen widths,
			 * or a single object that applies universally.»
			 * https://docs.connect.squareup.com/articles/adding-payment-form/#sqpaymentformparameters
			 * https://docs.connect.squareup.com/articles/adding-payment-form/#stylinginputinteriors
			 */
			inputStyles: [{
				fontFamily: 'sans-serif'
				,fontSize: '14px'
				,lineHeight: '20px'
				,padding: '5px 9px'
			}],
			postalCode: {elementId: this.dfCardPostalCodeId()},
			callbacks: {
				cardNonceResponseReceived: function(errors, nonce, cardData) {
					if (!errors) {
						_this.token = nonce;
						_this.placeOrderInternal();
					}
					else {
						/** @type {String[]} */
						var errorsA = [];
						errors.forEach(function(error) {
							errorsA.push(error.message);
						});
						_this.showErrorMessage(errorsA.join("\n"));
					}
				},
				paymentFormLoaded: function() {
					var postalCode = null;
					if (dfc.addressB()) {
						postalCode = dfc.addressB().postcode;
					}
					if (!postalCode && dfc.addressS()) {
						postalCode = dfc.addressS().postcode;
					}
					if (postalCode) {
						_this.square.setPostalCode(postalCode);
					}
					else {
						$.when(dfc.geo()).then(function(data) {
							_this.square.setPostalCode(data['zip_code']);
						});
					}
				}
			}
		});
		this.square.build();
	},
	/**
	 * 2016-09-28
	 * @returns {String[]}
	 */
	getCardTypes: function() {return ['VI', 'MC', 'AE', 'JCB', 'DI', 'DN', 'CUN'];},
	/**
	 * 2016-09-28
	 * @return {Object}
	*/
	initialize: function() {
		this._super();
		this.expirationComposite.subscribe(function(v) {
			/** @type {String[]} */
			var a = v.split('/');
			/** @type {String} */
			var year = $.trim(a[1]);
			if (2 === year.length) {
				year = '20' + year;
			}
			// 2016-09-28
			// https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Payment/Model/Config.php#L160-L175
			creditCardData.expirationYear = parseInt(year);
			// 2016-09-28
			// https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Payment/Model/Config.php#L141-L158
			creditCardData.expirationMonth = parseInt($.trim(a[0]));
		});
		/**
		 * 	2016-09-30
		 * 	Unlike all the other payment services,
		 * 	Square does not allow to populate the payment form fields programmatically,
		 * 	so the Magento 2 Swuare extension does not contain
		 * 	the standard «Prefill the Payment Form with Test Data?» option,
		 * 	and you should to fill the payment form manually each time.
		 * 	https://mage2.pro/t/2097
		 * 	https://docs.connect.squareup.com/articles/adding-payment-form/#populatingfieldsprogrammatically
		 */
		return this;
	},
	/**
	 * 2016-09-28
	 * @return {Object}
	*/
	initObservable: function() {
		this._super();
		this.observe(['expirationComposite']);
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
		this.square.requestCardNonce();
	}
});});
