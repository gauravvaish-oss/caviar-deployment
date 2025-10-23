define([
    'jquery',
    'mage/url',
    'mage/cookies',
    'Magento_Customer/js/customer-data'
], function ($, urlBuilder, cookies, customerData) {
    'use strict';

    return function () {

        // --- Increase / Decrease Quantity ---
       $(document).on('click', '.quantity-btn', function (e) {
            e.preventDefault(); // stop form submit

            const $btn = $(this);
            const $quantityBox = $btn.closest('.quantity-box');
            const $input = $quantityBox.find('.quantity-number');
            let qty = parseInt($input.val()) || 1;

            if ($btn.text().trim() === '+') {
                qty++;
            } else if (qty > 1) {
                qty--;
            }

            $input.val(qty); // update the input field

            // optional: disable - if qty=1
            $quantityBox.find('.quantity-btn:first').prop('disabled', qty <= 1);

            // Update Magento cart via AJAX
            updateCartQty($btn, qty);
        });


        // --- Remove Item from Cart ---
        $(document).on('click', '.remove-btn', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const removeUrl = $btn.attr('href');

            $.ajax({
                url: removeUrl,
                type: 'POST',
                data: { form_key: $.mage.cookies.get('form_key') },
                success: function () {
                    // Refresh minicart and totals
                    customerData.reload(['cart'], true);
                    $btn.closest('.cart-item').slideUp(300, function () {
                        $(this).remove();
                        updateCartTotals();
                    });
                },
                error: function () {
                    alert('Something went wrong while removing the item.');
                }
            });
        });

        // --- Apply Promo Code ---
        $(document).on('click', '.apply-btn', function (e) {
            e.preventDefault();
            const code = $('.form-control').val().trim();
            if (!code) return alert('Please enter a promo code.');

            $.ajax({
                url: urlBuilder.build('checkout/cart/couponPost'),
                type: 'POST',
                data: {
                    coupon_code: code,
                    remove: 0,
                    form_key: $.mage.cookies.get('form_key')
                },
                success: function (res) {
                    alert('Promo code applied successfully!');
                    customerData.reload(['cart'], true);
                    updateCartTotals();
                },
                error: function () {
                    alert('Invalid or expired promo code.');
                }
            });
        });

        // --- Function: Update Cart Quantity ---
        function updateCartQty($btn, qty) {
            const $item = $btn.closest('.cart-item');
            const itemId = $item.data('item-id');
            const updateUrl = urlBuilder.build('checkout/cart/updatePost');

            $.ajax({
                url: updateUrl,
                type: 'POST',
                data: {
                    form_key: $.mage.cookies.get('form_key'),
                    cart: { [itemId]: { qty: qty } }
                },
                success: function () {
                    customerData.reload(['cart'], true);
                    updateCartTotals();
                },
                error: function () {
                    alert('Error updating quantity. Please try again.');
                }
            });
        }

        // --- Function: Update Totals (Optional Simplified) ---
        function updateCartTotals() {
            $.ajax({
                url: urlBuilder.build('checkout/cart/totals'),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data && data.grand_total) {
                        $('.summary-total span:last-child').text(data.grand_total);
                    }
                }
            });
        }

        // Initial minicart sync
        customerData.reload(['cart'], true);
    };
});
