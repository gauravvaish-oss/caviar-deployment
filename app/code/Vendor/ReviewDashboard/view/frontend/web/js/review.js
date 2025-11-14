define([
    "jquery",
    "Magento_Ui/js/modal/modal"
], function ($, modal) {

    return function (config) {

        var options = {
            type: "popup",
            title: "Write a Review",
            modalClass: "review-modal",
            responsive: true,
            buttons: []
        };

        modal(options, $("#review-popup"));

        // OPEN POPUP WITH CORRECT PRODUCT
        $(".write-review-btn").on("click", function () {
            
            let productId = $(this).data("product-id");
            let productName = $(this).data("product-name");

            // assign correct product to form
            $("#review-product-id").val(productId);
            $("#review-product-name").text(productName);

            $("#review-popup").modal("openModal");
        });

        // SUBMIT AJAX
        $("#review-form").on("submit", function (e) {
            e.preventDefault();

            $.ajax({
                url: config.url,
                type: "POST",
                data: $(this).serialize(),
                showLoader: true,
                success: function (response) {

                    if (response.success) {

                        $("#review-success-message")
                            .text(response.message)
                            .fadeIn();

                        $("#review-form")[0].reset();

                        setTimeout(function () {
                            $("#review-success-message").fadeOut();
                            $("#review-popup").modal("closeModal");
                        }, 2000);

                    } else {

                        $("#review-success-message")
                            .css({background: "#f8d7da", color: "#721c24"})
                            .text(response.message)
                            .fadeIn();
                    }
                },
                error: function () {
                    alert("Error saving review.");
                }
            });
        });

    };
});
