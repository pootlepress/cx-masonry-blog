(function ($) {

    $(document).ready(function () {

        $('body').on( 'post-load', function () {
            // New posts have been added to the page.

            var newElements = [];
            $('#main > .block').each(function () {
                if ($(this).css('position') != 'absolute') {
                    newElements.push($(this).get()[0]);
                }
            });

            $('#main').masonry('appended', newElements);

            $('#main').imagesLoaded( function() { // fix overlapping images
                $('#main').masonry();
//                console.log('loaded');
            });
        });

        $('#main').append('<div class="gutter-sizer"></div>');
        $('#main').append('<div class="column-width"></div>');

        $('#main').masonry({
            columnWidth: '.column-width',
            gutter: '.gutter-sizer',
            itemSelector: '.block',
            isResizeBound: true,
            stamp: '#main > article'
        });

        $('#main').imagesLoaded( function() { // fix overlapping images
            $('#main').masonry();
        });

    });

})(jQuery);

