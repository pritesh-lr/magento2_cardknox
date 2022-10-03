/**
 * cardknox Google Pay for cart page
 **/
 define(["jquery","ifields","Magento_Checkout/js/model/quote"],function (jQuery,ifields,quote) {
    'use strict';
    var gPayConfig = window.checkoutConfig.payment.cardknox_google_pay;
    var quoteData = window.checkoutConfig.quoteData;
    var gPay = '';

    // Google pay object
    window.gpRequest = {
        merchantInfo: {
            merchantName: gPayConfig.merchantName
        },
        buttonOptions: {
            buttonColor: gPayConfig.button ? gPayConfig.button : "default",
            buttonType: GPButtonType.buy,
            buttonSizeMode: GPButtonSizeMode.full
        },
        
        billingParams: {
            emailRequired: window.checkoutConfig.quoteData.customer_is_guest == '1' ? true : false,
            billingAddressRequired: window.checkoutConfig.quoteData.customer_is_guest == '1' ? true : false,
            phoneNumberRequired: window.checkoutConfig.quoteData.customer_is_guest == '1' ? true : false,
            billingAddressFormat: GPBillingAddressFormat.full
        },
        shippingParams: {
            phoneNumberRequired: window.checkoutConfig.quoteData.customer_is_guest == '1' ? true : false,
            emailRequired: window.checkoutConfig.quoteData.customer_is_guest == '1' ? true : false,
        },
        onGetTransactionInfo: function () {
            let amt = getAmount();
            let countryCode = null;
            if(quote.shippingAddress() !== null && quote.shippingAddress() !== undefined ){
                countryCode = quote.shippingAddress().countryId
            } else {
                countryCode = "US"
            }
            return {
                displayItems: [
                    {
                        label: "Grandtotal",
                        type: "SUBTOTAL",
                        price: amt.toString(),
                    }
                ],
                countryCode: countryCode,
                currencyCode: quoteData.base_currency_code.toString(),
                totalPriceStatus: "FINAL",
                totalPrice: amt.toString(),
                totalPriceLabel: "Total"
            }
        },    
        onBeforeProcessPayment: function () {
            return new Promise(function (resolve, reject) {
                try {
                    if (gPay.validate() &&
                        gPay.additionalValidator()
                    ) {
                        // Check shipping method is selected or not in cart summary
                        if ( quote.shippingMethod() !== null && quote.shippingMethod() !== undefined) {
                            // update amount dynamically
                            window.ckGooglePay.updateAmount();
                            resolve(iStatus.success);
                        } else {
                            var err = 'Please select a shipping method.';
                            jQuery(".gpay-error").html("<div>"+err+" </div>").show();
                            setTimeout(function () { 
                                jQuery(".gpay-error").html("").hide();
                            }, 4000);
                            reject(err);
                        }
                    }
                } catch (err) {
                    jQuery(".gpay-error").html("<div> "+err+"</div>").show();
                    setTimeout(function () { 
                        jQuery(".gpay-error").html("").hide();
                    }, 4000);
                    reject(err);
                }
            });
        },
        onProcessPayment: function (paymentResponse) {
            paymentResponse =  JSON.parse(JSON.stringify(paymentResponse));
            var xAmount  = paymentResponse.transactionInfo.totalPrice;
            if (xAmount <= 0) {
                jQuery(".gpay-error").html("<div> Payment is not authorized. Invalid amount. Amount must be greater than 0 </div>").show();
                setTimeout(function () { 
                    jQuery(".gpay-error").html("").hide();
                }, 4000);
                throw "Payment is not authorized. Invalid amount. Amount must be greater than 0";
            } else {
                var token = btoa(paymentResponse.paymentData.paymentMethodData.tokenizationData.token);
                return gPay.startPlaceOrder(token, xAmount, paymentResponse);
            }
        },

        onPaymentCanceled: function(respCanceled) {
            jQuery(".gpay-error").html("<div> Payment was canceled </div>").show();
            setTimeout(function () { 
                jQuery(".gpay-error").html("").hide();
            }, 4000);
        },
        handleResponse: function (resp) {
            const respObj = JSON.parse(resp);
            if (respObj) {
                if (respObj.xError) {
                    setTimeout(function () { console.log(`There was a problem with your order (${respObj.xRefNum})!`) }, 500);
                } else
                    setTimeout(function () { console.log(`Thank you for your order (${respObj.xRefNum})!`) }, 500);
            }
        },
        getGPEnvironment: function () {
            return gPayConfig.GPEnvironment ? gPayConfig.GPEnvironment : "TEST";
        },
        initGP: function() {
            return {
                merchantInfo: this.merchantInfo,
                buttonOptions: this.buttonOptions,
                environment: this.getGPEnvironment(),
                billingParameters: this.billingParams,
                shippingParameters: {
                    emailRequired: this.shippingParams.emailRequired,
                },
                onGetTransactionInfo: "gpRequest.onGetTransactionInfo",
                onBeforeProcessPayment: "gpRequest.onBeforeProcessPayment",
                onProcessPayment: "gpRequest.onProcessPayment",
                onPaymentCanceled: "gpRequest.onPaymentCanceled",
                onGPButtonLoaded: "gpRequest.gpButtonLoaded",
                isDebug: isDebugEnv
            };
        },
        gpButtonLoaded: function(resp) {
            if (!resp) return;
            if (resp.status === iStatus.success) {
                showHide("divGpay", true);
            } else if (resp.reason) {
                jQuery(".gpay-error").html("<div>"+resp.reason+"</div>").show();
            }
        },
    };
    
    function showHide(elem, toShow) {
        if (typeof(elem) === "string") {
            elem = document.getElementById(elem);
        }
        if (elem) {
            toShow ? elem.classList.remove("hidden") : elem.classList.add("hidden");
        }
    }
    function getAmount () {
        var totals = quote.totals();
        var base_grand_total = (totals ? totals : quote)['base_grand_total'];
        return parseFloat(base_grand_total).toFixed(2);
    }
    return {
        init: function (parent) {
            // No parent
            if (!parent) {
                return;
            }
            gPay = parent;
            if (gPayConfig.merchantName == "" || gPayConfig.merchantName == null || gPayConfig.merchantName.length == 0) {
                jQuery(".gpay-error").html("<div>Please contact to store owner. Failed to initalized Google Pay button. </div>").show();
            } else {
                jQuery('#igp').attr('data-ifields-oninit',"window.gpRequest.initGP");
                ckGooglePay.enableGooglePay();
            }
        }
    };
});