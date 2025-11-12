jQuery(document).ready(function($) {
    // Use event delegation for dynamically loaded elements
    $(document).on('click', '.gmt-element-21935ea .gmt-icon i.fas.fa-bars', function() {
        $('nav[data-action="navigation"]').slideToggle(400); // toggle menu
        $(this).toggleClass('active'); // rotate icon
    });
});
