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
	 * 2017-10-09
	 * These data are submitted to the M2 server part
	 * as the `additional_data` property value on the «Place Order» button click:
	 * @used-by Df_Payment/mixin::getData():
	 *		getData: function() {return {additional_data: this.dfData(), method: this.item.method};},
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L224
	 * @override
	 * @see Dfe_StripeClone/main::dfData()
	 * @returns {Object}
	 */
	dfData: function() {return df.o.merge(this._super(), {
		cardholder: this.cardholder(), postalCode: this.postalCode
	});},
    /**
	 * 2016-09-28 https://mage2.pro/t/1936
	 * 2017-10-16
	 * Magento <= 2.1.0 calls an `afterRender` handler outside of the `this` context.
	 * It passes `this` to an `afterRender` handler as the second argument:
	 * https://github.com/magento/magento2/blob/2.0.9/app/code/Magento/Ui/view/base/web/js/lib/ko/bind/after-render.js#L19
	 * Magento >= 2.1.0 calls an `afterRender` handler within the `this` context:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Ui/view/base/web/js/lib/knockout/bindings/after-render.js#L20
	 * @used-by Dfe_Square/atTheEnd.html
	 * @param {HTMLElement} element
	 * @param {Object} _this
	 */
    dfOnRender: _.after(2, function(element, _this) {$.proxy(function() {
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
				 * Note 1.
				 * «Called when the generation of a nonce completes (or an error occurs during generation).»
				 * Required.
				 * An example: https://github.com/square/connect-api-examples/blob/8b317991/connect-examples/v2/php_payment/index.html#L56-L76
				 * Note 2. «Square Connect API» → «Nonce callback format»
				 * The function you specify for the `cardNonceResponseReceived` callback
				 * takes three parameters, like so:
				 * 		cardNonceResponseReceived: function(errors, nonce, cardData) {
				 * 			// Handle the nonce
				 * 		}
				 * 	<...>
				 The `cardData` parameter is an object that contains non-confidential information
				 about the buyer's credit card (or null if any errors occurred).
				 It has the following format:
				 *		{
				 *			"card_brand": "VISA",
				 *			"last_4": "1111",
				 *			"exp_month": 11,
				 *			"exp_year": 2016,
				 *			"billing_postal_code": "94103"
				 *		}
				 * https://docs.connect.squareup.com/articles/adding-payment-form#noncecallbackformat
				 */
				cardNonceResponseReceived: $.proxy(function(errors, nonce) {
					/**
					 * 2017-10-06
					 * «The `errors` parameter is an array of objects
					 * that describe any errors that occurred during nonce creation.
					 * If no errors occurred, this is null.
					 * See «Nonce generation errors» for the object format and a list of the most common errors.»
					 * https://docs.connect.squareup.com/articles/adding-payment-form#noncecallbackformat
					 * https://docs.connect.squareup.com/articles/adding-payment-form#noncegenerationerrors
					 */
					if (!errors) {
						/**
						 * 2017-10-06
						 * «The `nonce` parameter is simply the string value of the nonce,
						 * which you send to your server and then along to the Charge endpoint.
						 * This value is null if any errors occurred.»
						 * https://docs.connect.squareup.com/articles/adding-payment-form#noncecallbackformat
						 */
						this.token = nonce;
						this.placeOrderInternal();
					}
					else {
						/** @type {String[]} */ var errorsA = [];
						errors.forEach(function(error) {
							errorsA.push(error.message);
						});
						// 2017-10-06
						// We should use `<br/>`, not `\n`:
						// «Multiple error messages are wrongly shown on the same line»
						// https://github.com/mage2pro/square/issues/14
						this.showErrorMessage(errorsA.join("<br/>"));
						this.state_waitingForServerResponse(false);
					}
				}, this)
				/**
				 * 2017-10-06
				 * Note 1. «Square Connect API» → «SqPaymentForm callbacks»
				 * «Called when one of a variety of events occurs
				 * while a buyer is filling out the payment form.
				 * See `Working with payment form input events` for details.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformcallbacks
				 *
				 * Note 2. «Square Connect API» → «Working with payment form input events»
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
				,inputEventReceived: $.proxy(function(event) {
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
				}, this)
				/**
				 * 2017-10-06
				 * Note 1. «Square Connect API» → «SqPaymentForm callbacks»
				 * «Called when all payment form iframes have successfully been added to the webpage.
				 * Indicates that payment form functions like `setPostalCode` can now be called.
				 * See Populating form fields programmatically for details.»
				 *
				 * Note 2. «Square Connect API» → «Populating form fields programmatically»
				 * «You can use the SqPaymentForm's `setPostalCode` function
				 * to populate the postal code field programmatically.
				 * This is convenient for a buyer that has already provided their postal code elsewhere,
				 * such as when specifying their shipping address.
				 * The buyer can always replace the value you populate.
				 *
				 * You should call the `setPostalCode` function
				 * in the `paymentFormLoaded` callback you specify when initializing the payment form,
				 * as shown in Sample webpage:
				 * https://docs.connect.squareup.com/articles/adding-payment-form#samplewebpage
				 * This ensures that the form is fully loaded when you call the function.
				 * This function will fail if you call it before the payment form is finished loading
				 * 		paymentFormLoaded: function() {
				 * 			paymentForm.setPostalCode('94103');
				 * 		}
				 * Calling this function fires the `postalCodeChanged` input event described in Input event types.
				 * https://docs.connect.squareup.com/articles/adding-payment-form#inputeventtypes
				 * You cannot programmatically populate payment form fields besides the postal code field.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form#populatingfieldsprogrammatically
				 */
				,paymentFormLoaded: $.proxy(function() {
					/** @type {?String} */ var postalCode = null;
					/** @type {?Object} */ var a;
					if (a = dfc.addressB()) {
						postalCode = a.postcode;
					}
					if (!postalCode && (a = dfc.addressS())) {
						postalCode = a.postcode;
					}
					if (postalCode) {
						this.square.setPostalCode(postalCode);
						this.postalCode = postalCode;
					}
					else {
						$.when(dfc.geo()).then($.proxy(function(data) {
							this.postalCode = data['zip_code'];
							this.square.setPostalCode(this.postalCode);
						}), this)
					}
				}, this)
				/**
				 * 2017-10-06
				 * «Square Connect API» → «SqPaymentForm callbacks»
				 * «Called when the SqPaymentForm detects that the buyer is using an unsupported browser.
				 * Recommended.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
				 * 2017-10-07
				 * «Square Connect API» → «Unsupported browsers»
				 * «If the `SqPaymentForm` does not support a buyer's current browser, it calls the function
				 * that you specified as the `unsupportedBrowserDetected` callback during initialization, if any.
				 * Specifying this callback lets you signal to the buyer in any way you choose
				 * that their browser is not supported.
				 * The `SqPaymentForm` supports most modern browsers.
				 * Versions of Internet Explorer prior to Internet Explorer 10 are not supported.
				 * NOTE: Square's integration with Apple Pay
				 * adheres to Apple's development requirements for Apple Pay and is only supported for:
				 * 		iOS 10 and later:
				 * 				Apple Pay JavaScript is supported on all iOS devices with a Secure Element.
				 * 				It is supported both in Safari and `SFSafariViewController objects`.
				 * 		MacOS 10.12 and later:
				 * 				Apple Pay JavaScript is supported in Safari.
				 * 				The user must have an iPhone or Apple Watch to authorize the payment,
				 * 				or a MacBook Pro with Touch ID.»
				 * https://docs.connect.squareup.com/articles/adding-payment-form#unsupportedbrowsers
				 */
				,unsupportedBrowserDetected: $.proxy(function() {
					this.showErrorMessage('Unfortunately, your browser does not support this payment option.');
				}, this),
			}
			/**
			 * 2017-10-06
			 * «Defines the details of the input field for the buyer's card number.
			 * The only field you must provide in this object is `elementId`,
			 * which specifies the id of the element on your page that will be replaced with the card number input.
			 * You can also specify placeholder text, which is shown when the input is empty.»
			 * Type: object.
			 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
			 */
			,cardNumber: {elementId: this.dfCardNumberId()}
			/**
			 * 2017-10-06
			 * «Defines the details of the input field for the buyer's card CVV.
			 * This object has the same format as the object you specify for `cardNumber`.»
			 * Type: object.
			 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
			 */
			,cvv: {elementId: this.dfCardVerificationId()}
			/**
			 * 2017-10-06
			 * «Defines the details of the input field for the buyer's card expiration date.
			 * This object has the same format as the object you specify for `cardNumber`.»
			 * Type: object.
			 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
			 */
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
			/**
			 * 2017-10-06
			 * Note 1. «Square Connect API» → «SqPaymentForm parameters»
			 * «Defines the details of the input field for the buyer's card postal code.
			 * This object has the same format as the object you specify for cardNumber.
			 * If object set to false, postal code field will not load.
			 * Warning: for United States, Canada, or United Kingdom
			 * merchants this will result in declining all charges
			 * as Postal Code is a required field in these countries.»
			 * Type: object.
			 * https://docs.connect.squareup.com/articles/adding-payment-form#sqpaymentformparameters
			 *
			 * Note 2. «Square Connect API» → «Remove Postal Code Field»
			 * «You can remove the postal code field by setting the postal code block from:
			 *		postalCode: {
			 *			elementId: 'sq-postal-code'
			 *		}
			 * to:
			 * 		postalCode: false
			 * Important: For United States, Canada, and United Kingdom merchants
			 * this will result in declining all charges as Postal Code is a required field in these countries.»
			 * https://docs.connect.squareup.com/articles/adding-payment-form#removepostalcodefield
			 */
			,postalCode: {elementId: this.dfCardPostalCodeId()}
		});
		/**
		 * 2017-10-06
		 * Generating the payment form
		 * In most cases, the SqPaymentForm automatically generates form fields on your webpage
		 * when you initialize it.
		 * However, the form does not automatically generate its fields
		 * if it never detects a `DOMContentLoaded` event.
		 * This occurs most commonly in single-page web applications
		 * when the form is initialized long after the `DOMContentLoaded` event has fired.
		 * In this case, you can generate the payment form with an extra line of Javascript, like so:
		 * 		// Call this after initializing the payment form _and_ after the DOM is fully loaded
		 * 		paymentForm.build();
		 * Calling `build` on an `SqPaymentForm` that has already been generated
		 * will log an error to the Javascript console, but it is otherwise harmless.
		 * https://docs.connect.squareup.com/articles/adding-payment-form#generatingpaymentform
		 */
		this.square.build();
	}, _this)()}),
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
		// so the Magento 2 Square extension does not contain
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
		/**
		 * 2017-10-06 «Square Connect API» → «Obtaining a nonce»
		 * «When a buyer has entered their card details into the `SqPaymentForm`
		 * and has indicated that they're ready to pay,
		 * your webpage should call the `requestCardNonce` function of your `SqPaymentForm`, like so:
		 * 		// Call this to obtain a card nonce from a populated `SqPaymentForm`
		 * 		paymentForm.requestCardNonce();
		 * This tells the `SqPaymentForm` to generate a nonce from the buyer's card details.
		 * When nonce generation completes,
		 * the form will execute the callback you specified in the `cardNonceResponseReceived` field
		 * (as demonstrated in Initializing the payment form).
		 * https://docs.connect.squareup.com/articles/adding-payment-form#settinguppaymentform
		 * The generated nonce is provided as a parameter to this callback.
		 * When you receive the nonce,
		 * you're ready to kick off the process to submit all relevant payment information
		 * (including the nonce and the amount to charge) to your server.
		 * Important: Card nonces expire after 24 hours.»
		 * https://docs.connect.squareup.com/articles/adding-payment-form#obtainingnonce
		 */
		if (this.isNewCardChosen()) {
			this.square.requestCardNonce();
		}
		else {
			this.token = this.currentCard();
			this.placeOrderInternal();
		}
	}
});});
