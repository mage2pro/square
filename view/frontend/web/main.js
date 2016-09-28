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
				,padding: '6px 9px'
			}],
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
					//debugger;
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
