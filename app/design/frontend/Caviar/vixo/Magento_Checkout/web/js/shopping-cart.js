/**
 * Custom override of Magento_Checkout/js/shopping-cart.js
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'jquery-ui-modules/widget',
    'mage/translate'
], function ($, confirm) {
    'use strict';

    $.widget('mage.shoppingCart', {
        /** @inheritdoc */
        _create: function () {
            var items, i, reload;

            $(this.options.emptyCartButton).on('click', $.proxy(function () {
                this._confirmClearCart();
            }, this));

            items = $.find('[data-role="cart-item-qty"]');

            for (i = 0; i < items.length; i++) {
                // Handle Enter key
                $(items[i]).on('keypress', $.proxy(function (event) {
                    var keyCode = event.keyCode ? event.keyCode : event.which;

                    if (keyCode == 13) { // Enter key
                        $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                        $(this.options.updateCartActionContainer)
                            .attr('name', 'update_cart_action').attr('value', 'update_qty');
                    }
                }, this));

                // NEW: Auto update cart on quantity change
                $(items[i]).on('change', $.proxy(function (event) {
                    this._autoUpdateCart($(event.currentTarget));
                }, this));
            }

            $(this.options.continueShoppingButton).on('click', $.proxy(function () {
                location.href = this.options.continueShoppingUrl;
            }, this));

            $(document).on('ajax:removeFromCart', $.proxy(function () {
                reload = true;
                $('div.block.block-minicart').on('dropdowndialogclose', $.proxy(function () {
                    if (reload === true) {
                        location.reload();
                        reload = false;
                    }
                    $('div.block.block-minicart').off('dropdowndialogclose');
                }));
            }, this));

            $(document).on('ajax:updateItemQty', $.proxy(function () {
                reload = true;
                $('div.block.block-minicart').on('dropdowndialogclose', $.proxy(function () {
                    if (reload === true) {
                        location.reload();
                        reload = false;
                    }
                    $('div.block.block-minicart').off('dropdowndialogclose');
                }));
            }, this));
        },

        /**
         * Auto update cart when quantity changes
         * @private
         */
        _autoUpdateCart: function (inputElement) {
            clearTimeout(this._autoUpdateTimer);
            this._autoUpdateTimer = setTimeout($.proxy(function () {
                $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                $(this.options.updateCartActionContainer)
                    .attr('name', 'update_cart_action').attr('value', 'update_qty');

                var form = inputElement.closest('form');
                if (form.length) {
                    form.trigger('submit');
                }
            }, this), 500); // debounce
        },

        _confirmClearCart: function () {
            var self = this;

            confirm({
                content: $.mage.__('Are you sure you want to remove all items from your shopping cart?'),
                actions: {
                    confirm: function () {
                        self.clearCart();
                    }
                }
            });
        },

        clearCart: function () {
            $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
            $(this.options.updateCartActionContainer)
                .attr('name', 'update_cart_action').attr('value', 'empty_cart');

            if ($(this.options.emptyCartButton).parents('form').length > 0) {
                $(this.options.emptyCartButton).parents('form').trigger('submit');
            }
        }
    });

    return $.mage.shoppingCart;
});
