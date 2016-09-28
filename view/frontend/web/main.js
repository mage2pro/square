// 2016-09-28
define ([
	'Df_Payment/card'
	,'Dfe_Square/API'
	,'jquery'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
], function(parent, Square, $, creditCardData) {'use strict'; return parent.extend({
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
		var paymentForm = new SqPaymentForm({
			applicationId: this.config('applicationID'),
			cardNumber: {
				elementId: this.dfCardNumberId(),
				placeholder: '•••• •••• •••• ••••'
			},
			cvv: {
				elementId: this.dfCardVerificationId(),
				placeholder: 'CVV'
			},
			expirationDate: {
				elementId: this.dfCardExpirationCompositeId(),
				placeholder: 'MM/YY'
			},
			// 2016-09-28
			// Это поле является обязательным.
			inputClass: 'dummy',
			postalCode: {elementId: this.dfCardPostalCodeId()},
			callbacks: {
				cardNonceResponseReceived: function(errors, nonce, cardData) {
					if (errors) {
						console.log("Encountered errors:");
						errors.forEach(function(error) {console.log('  ' + error.message);});
					}
					else {
						alert('Nonce received: ' + nonce);
					}
				},
				paymentFormLoaded: function() {
					debugger;
					paymentForm.setPostalCode('94103');
					// Fill in this callback to perform actions after the payment form is
					// done loading (such as setting the postal code field programmatically).
					// paymentForm.setPostalCode('94103');
				}
			}
		});
		paymentForm.build();
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
		if (this.validate()) {
		}
	}
});});
