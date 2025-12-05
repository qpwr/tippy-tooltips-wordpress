/**
 * Initialize Tippy.js
 */

;(function($) {

    // Initialize Tippy tooltips
    tippy( '[data-tooltip-template]', {
        allowHTML: true,
        content(reference) {
            const id = reference.getAttribute('data-tooltip-template');
            const template = document.getElementById(id);
            return template.innerHTML;
        },
    });

    // Remove title attribute where tooltip is present
    $( '[data-tippy-content]' ).each( function() {
        $( this ).removeAttr( 'title' );
    });

})( window.jQuery );
