// 2016-09-28
// 2017-10-05 E-commerce API: https://docs.connect.squareup.com/articles/paymentform-overview
define([
	'df', 'df-lodash', 'Df_Checkout/data', 'Df_StripeClone/main', 'jquery'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
	,'https://js.squareup.com/v2/paymentform'
], function(df, _, dfc, parent, $, creditCardData) {'use strict';
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
    dfOnRender: _.after(2, function() {
		var _this = this;
		// 2017-10-06
		// `SqPaymentForm` parameters:
		// https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
		this.square = new SqPaymentForm({
			// 2017-10-06
			// «Your application's ID, available from the application dashboard.
			// While you're testing out the e-commerce API,
			// you should provide your sandbox application ID.»
			// Type: string.
			applicationId: this.publicKey()
			/**
			 * 2017-10-06
			 * «Defines callbacks that are executed when certain payment form events occur.
			 * The only field you must provide in this object is `cardNonceResponseReceived`,
			 * which is a callback that's executed
			 * when the form generates a nonce from the buyer's card details.
			 * See `Appendix: SqPaymentForm callbacks` for a list of all available callbacks:
			 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformcallbacks»
			 * Type: object.
			 */
			,callbacks: {
				/**
				 * 2017-10-06
				 * «Called when the generation of a nonce completes (or an error occurs during generation).»
				 * Required.
				 * An example: https://github.com/square/connect-api-examples/blob/8b317991/connect-examples/v2/php_payment/index.html#L56-L76
				 */
				cardNonceResponseReceived: $.proxy(function(errors, nonce, cardData) {
					if (!errors) {
						this.token = nonce;
						//this.placeOrderInternal();
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
				/**
				 * 2017-10-06
				 * Note 1. «SqPaymentForm callbacks»
				 * «Called when one of a variety of events occurs
				 * while a buyer is filling out the payment form.
				 * See `Working with payment form input events` for details.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformcallbacks
				 *
				 * Note 2. «Working with payment form input events»
				 * «While a buyer is filling in the fields of the payment form,
				 * the `inputEventReceived` callback function you specified during initalization
				 * is called every time certain events occur
				 * (for example, every time a form field gains or loses focus).
				 * Every inputEvent object sent to the callback function has the following structure:
				 *	{
				 *		cardBrand: "masterCard",
				 *		elementId: "sq-card-number",
				 *		eventType: "focusClassRemoved",
				 *		field: "cardNumber",
				 *		currentState: {
				 *			hasErrorClass: false,
				 *			hasFocusClass: false,
				 *			isCompletelyValid: true,
				 *			isPotentiallyValid: true
				 *		},
				 *		previousState: {
				 *			hasErrorClass: false,
				 *			hasFocusClass: false,
				 *			isCompletelyValid: false,
				 *			isPotentiallyValid: true
				 *		}
				 *	}
				 * »
				 * https://docs.connect.squareup.com/articles/adding-payment-form#inputevents
				 *
				 * Note 3.
				 * An example: https://github.com/square/connect-api-examples/blob/8b317991/connect-examples/v2/rails_payment/app/views/welcome/jquery.html.erb#L223-L246
				 */
				inputEventReceived: $.proxy(function(event) {
					/**
					 * 2017-10-06
					 * @var {Object} event
					 * @property {String} event.cardBrand
					 * @property {String} event.eventType
					 * https://docs.connect.squareup.com/articles/adding-payment-form#inputevents
					 */
					/**
					 * 2017-10-06
					 * «Square Connect API» → «Input event types»
					 * https://docs.connect.squareup.com/articles/adding-payment-form#inputeventtypes
					 * `cardBrandChanged`:
					 * 		«The payment form detected a new likely credit card brand based on the card number»
					 */
					if ('cardBrandChanged' === event.eventType) {
						/**
						 * 2017-10-06
						 * Note 1. «Square Connect API» → «Card brands»
						 * https://docs.connect.squareup.com/articles/adding-payment-form#cardbrands
						 * Note 2. I have implemented it similar to @see Magento_Payment/cc-form::initialize():
						 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L55-L79
						 */
						this.selectedCardType(df.tr(event.cardBrand, {
							americanExpress: 'AE'
							,discover: 'DI'
							,discoverDiners: 'DN'
							,JCB: 'JCB'
							,masterCard: 'MC'
							,unionPay: 'CUN'
							,visa: 'VI'
							,unknown: null
						}));
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
			,inputStyles: [{fontFamily: 'sans-serif', fontSize: '14px', lineHeight: '20px', padding: '5px 9px'}]
			,postalCode: {elementId: this.dfCardPostalCodeId()}
		});
		this.square.build();
	}),
	/**
	 * 2016-09-28
	 * 2017-02-05 The bank card network codes: https://mage2.pro/t/2647
	 * 2017-10-06 «Square Connect API» → «Card brands»:
	 * https://docs.connect.squareup.com/articles/adding-payment-form#cardbrands
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
		// 2017-10-06
		// Without it, the MasterCard brand will be initially (with empty Credit Card Number» field) highlighted.
		this.selectedCardType(null);
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
