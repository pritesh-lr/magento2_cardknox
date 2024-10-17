define([
    'jquery',
    'ko',
    'uiComponent',
    'CardknoxDevelopment_Cardknox/js/action/get-giftcard',
    'CardknoxDevelopment_Cardknox/js/action/add-giftcard',
    'CardknoxDevelopment_Cardknox/js/action/cancel-giftcard',
    'CardknoxDevelopment_Cardknox/js/model/giftcard',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote'
],function ($, ko, Component, getGiftCardAction, addGiftCardAction, cancelGiftCardAction, giftCardAccount, customerData, quote)
{
    'use strict';

    var checkoutCkGiftCard = customerData.get('checkout_ckgiftcard'),
        ckGiftCardCodeSession = checkoutCkGiftCard().ckgiftcard_code,

        ckGiftCardCode = giftCardAccount.getCkGiftCardCode(),
        isCkGiftCardApplied = giftCardAccount.getIsCkGiftCardApplied();
        if (ckGiftCardCodeSession) {
            ckGiftCardCode(ckGiftCardCodeSession);
            isCkGiftCardApplied(true);
        }

    return Component.extend({

        defaults: {
            template: 'CardknoxDevelopment_Cardknox/checkout/giftcard',
        },
        isLoading: getGiftCardAction.isLoading,
        giftCardAccount: giftCardAccount,

        ckGiftCardCode: ckGiftCardCode,
        isCkGiftCardApplied: isCkGiftCardApplied,

        /**
          @returns {}
         */
        initialize: function () {
            this._super();
            var grandTotal = quote.totals().grand_total;
            console.log("grandTotal :- " + grandTotal);
            $('#cancel-gift-card').trigger('click');
            if (this.isDisplayedCKGiftcard() === false) {
                var sections = ['cart'];
                customerData.reload(sections, true);
            }
        },

        /**
         * Apply gift card
         */
        isDisplayedCKGiftcard: function(value) {
            return window.checkoutConfig.payment.cardknox.isEnabledCardknoxGiftcard;
        },

        /**
         * Check gift card account balance
         */
        checkBalance: function() {
            if (this.validate()) {
                getGiftCardAction.check(this.ckGiftCardCode());
            }
        },

        /**
         * Apply gift card
         */
        addGiftCard: function(value) {
            if (this.validate()) {
                addGiftCardAction.add(this.ckGiftCardCode(), isCkGiftCardApplied);
            }
        },

        /**
         * Cancel gift card
         */
        cancelGiftCard: function(value) {
            if (this.validate()) {
                cancelGiftCardAction.cancel(this.ckGiftCardCode(), isCkGiftCardApplied);
            }
        },

        /**
         * Validate gift card form
         *
         * @returns {boolean}
         */
        validate: function() {
            var form = '#ckgiftcard-form';
            return $(form).validation() && $(form).validation('isValid');
        },
    });
});