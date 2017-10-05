// 2016-09-28
// 2017-10-05 E-commerce API: https://docs.connect.squareup.com/articles/paymentform-overview
define([
	'df', 'Df_Checkout/data', 'Df_StripeClone/main', 'jquery'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
	,'https://js.squareup.com/v2/paymentform'
], function(df, dfc, parent, $, creditCardData) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
return parent.extend({
	/**
	 * 2016-11-09
	 * The «Square» payment form works only if your checkout page is loaded over HTTPS:
	 * https://mage2.pro/t/2259
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @returns {String}
	 */
	debugMessage: df.c(function() {
		return 'https:' === window.location.protocol ? '' : '<span class="df-error">The payment form must be generated on a webpage that uses <b>HTTPS</b>, with one exception: you can test on <b>localhost</b> without using HTTPS: <a href="https://mage2.pro/t/2259" target="_blank" title="Mage2.PRO support forum">https://mage2.pro/t/2259</a></span>'
		;
	}),
	defaults: {
		df: {
			card: {
				expirationComposite: 'expirationComposite'
				,field: {expiration: 'Dfe_Square/expiration'}
				,new: {atTheEnd: 'Dfe_Square/atTheEnd'}
			}
		}
		,expirationComposite: ''
	},
	/**
	 * 2016-09-28
	 * Dfe_Square/expiration.html
	 * @returns {String[]}
	 */
	dfCardExpirationCompositeId: function() {return this.fid('expiration_composite');},
	/**
	 * 2016-09-28
	 * @used-by Dfe_Square/atTheEnd.html
	 * @returns {String[]}
	 */
	dfCardPostalCodeId: function() {return this.fid('postal_code');},
    /**
	 * 2016-09-28 https://mage2.pro/t/1936
	 * @used-by Dfe_Square/atTheEnd.html
	 */
    dfOnRender: function() {
    	this.renderCount = 1 + (this.renderCount || 0);
    	if (2 === this.renderCount) {
			var _this = this;
			this.square = new SqPaymentForm({
				applicationId: this.publicKey()
				,callbacks: {
					cardNonceResponseReceived: $.proxy(function(errors, nonce, cardData) {
						if (!errors) {
							this.token = nonce;
							this.placeOrderInternal();
						}
						else {
							/** @type {String[]} */ var errorsA = [];
							errors.forEach(function(error) {
								errorsA.push(error.message);
							});
							this.showErrorMessage(errorsA.join("\n"));
							this.state_waitingForServerResponse(false);
						}
					}, this),
					paymentFormLoaded: $.proxy(function() {
						var postalCode = null;
						if (dfc.addressB()) {
							postalCode = dfc.addressB().postcode;
						}
						if (!postalCode && dfc.addressS()) {
							postalCode = dfc.addressS().postcode;
						}
						if (postalCode) {
							this.square.setPostalCode(postalCode);
						}
						else {
							$.when(dfc.geo()).then(function(data) {_this.square.setPostalCode(data['zip_code']);});
						}
					}, this)
				}
				,cardNumber: {elementId: this.dfCardNumberId(),}
				,cvv: {elementId: this.dfCardVerificationId()}
				,expirationDate: {elementId: this.dfCardExpirationCompositeId(), placeholder: 'MM/YY'}
				/**
				 * 2016-09-28 Это поле является обязательным.
				 * 2016-09-29
				 * «This CSS class is assigned to all four of the iframes generated for the payment form.
				 * You can create CSS rules for this class to style the exterior of the inputs
				 * (i.e., borders and margins). See Styling input exteriors for more information.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form/#sqpaymentformparameters
				 */
				,inputClass: 'dfe-square'
				/**
				 * 2016-09-29
				 * «Each object in this array defines styles to apply
				 * to the interior of the payment form inputs.
				 * The array can include multiple objects that apply to different ranges of screen widths,
				 * or a single object that applies universally.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form/#sqpaymentformparameters
				 * https://docs.connect.squareup.com/articles/adding-payment-form/#stylinginputinteriors
				 */
				,inputStyles: [{
					fontFamily: 'sans-serif', fontSize: '14px', lineHeight: '20px', padding: '5px 9px'
				}]
				,postalCode: {elementId: this.dfCardPostalCodeId()}
			});
			this.square.build();
		}
	},
	/**
	 * 2016-09-28
	 * 2017-02-05 The bank card network codes: https://mage2.pro/t/2647
	 * @returns {String[]}
	 */
	getCardTypes: function() {return ['VI', 'MC', 'AE', 'JCB', 'DI', 'DN', 'CUN'];},
	/**
	 * 2016-09-28
	 * @override
	 * @see Df_Payment/card::initialize()
	 * https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/card.js#L77-L110
	 * @returns {exports}
	*/
	initialize: function() {
		this._super();
		this.expirationComposite.subscribe(function(v) {
			/** @type {String[]} */ var a = v.split('/');
			/** @type {String} */ var year = $.trim(a[1]);
			if (2 === year.length) {
				year = '20' + year;
			}
			// 2016-09-28
			// https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Payment/Model/Config.php#L160-L175
			creditCardData.expirationYear = df.int(year);
			// 2016-09-28
			// https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Payment/Model/Config.php#L141-L158
			creditCardData.expirationMonth = df.int($.trim(a[0]));
		});
		// 2016-09-30
		// Unlike other payment services,
		// Square does not allow to populate the payment form fields programmatically,
		// so the Magento 2 Swuare extension does not contain
		// the standard «Prefill the Payment Form with Test Data?» option,
		// and you should to fill the payment form manually each time.
		// https://mage2.pro/t/2097
		// https://docs.connect.squareup.com/articles/adding-payment-form/#populatingfieldsprogrammatically
		return this;
	},
	/**
	 * 2016-09-28
	 * @override
	 * @see Df_Payment/card::initObservable()
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/card.js#L141-L157
	 * @used-by Magento_Ui/js/lib/core/element/element::initialize()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.3/app/code/Magento/Ui/view/base/web/js/lib/core/element/element.js#L104
	 * @returns {Element} Chainable
	*/
	initObservable: function() {this._super(); this.observe(['expirationComposite']); return this;},
	/**
	 * 2016-09-28
	 * @override
	 * @see Df_StripeClone/main::placeOrder()
	 * @used-by Df_Payment/main.html:
	 *		<button
	 *			class="action primary checkout"
	 *			type="submit"
	 *			data-bind="
	 *				click: placeOrder
	 *				,css: {disabled: !isPlaceOrderActionAllowed()}
	 *				,enable: dfIsChosen()
	 *			"
	 *			disabled
	 *		>
	 *			<span data-bind="df_i18n: 'Place Order'"></span>
	 *		</button>
	 * https://github.com/mage2pro/core/blob/2.9.10/Payment/view/frontend/web/template/main.html#L57-L68
	 * https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	 * @param {this} _this
	 * @param {Event} event
	*/
	placeOrder: function(_this, event) {
		if (event) {
			event.preventDefault();
		}
		// 2017-07-26 «Sometimes getting duplicate orders in checkout»: https://mage2.pro/t/4217
		this.state_waitingForServerResponse(true);
		this.square.requestCardNonce();
	}
});});
