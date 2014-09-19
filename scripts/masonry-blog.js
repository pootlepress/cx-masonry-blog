(function ($) {

    $(document).ready(function () {

        $('<div id="masonry"></div>').appendTo($('#main'));
        $('#main > .block').appendTo($('#masonry'));

        $('body').on( 'post-load', function () {
            // New posts have been added to the page.

            var newElements = [];
            $('#masonry > .block').each(function () {
                if ($(this).css('position') != 'absolute') {
                    newElements.push($(this).get()[0]);
                }
            });

            $('#masonry').masonry('appended', newElements);

            $('#masonry').imagesLoaded( function() { // fix overlapping images
                $('#masonry').masonry();
//                console.log('loaded');
            });
        });

        $('#masonry').append('<div class="gutter-sizer"></div>');
        $('#masonry').append('<div class="column-width"></div>');

        $('#masonry').masonry({
            columnWidth: '.column-width',
            gutter: '.gutter-sizer',
            itemSelector: '.block',
            isResizeBound: true,
            stamp: '#mansory .stamp'
        });

        $('#masonry').imagesLoaded( function() { // fix overlapping images
            $('#masonry').masonry();
        });
    });

})(jQuery);

