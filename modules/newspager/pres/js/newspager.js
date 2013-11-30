$(document).ready(function () {
    var APFNP = {};

    APFNP.page = 1;
    APFNP.newsPager = $('.apf-news-pager');
    APFNP.totalPages = APFNP.newsPager.data('news-count');
    APFNP.lang = APFNP.newsPager.data('lang');
    APFNP.errorMessage = APFNP.newsPager.data('error-message');
    APFNP.actionUrl = APFNP.newsPager.data('action-url');
    APFNP.dataDir = APFNP.newsPager.data('data-dir');

    APFNP.getUrl = function (page) {
        return APFNP.actionUrl + 'page/' + page + '/lang/' + APFNP.lang + '/datadir/' + APFNP.dataDir;
    };
    APFNP.activate = function(){
        APFNP.newsPager.fadeTo('fast', '1');
    };
    APFNP.deactivate = function(){
        APFNP.newsPager.fadeTo('fast', '0.3');
    };
    APFNP.getContent = function (direction) {
        APFNP.deactivate();

        if (direction == 'prev') {
            APFNP.page--;
            if (APFNP.page == 0) {
                APFNP.page = 1;
                APFNP.activate();
                return;
            }
        } else {
            APFNP.page++;
            if (APFNP.page > APFNP.totalPages) {
                APFNP.page = APFNP.totalPages;
                APFNP.activate();
                return;
            }
        }
        $.ajax({
            dataType: 'json',
            cache: false,
            url: APFNP.getUrl(APFNP.page),
            success: function (data, textStatus, jqXHR) {
                $('.apf-news-pager .news h3').html(data.headline);
                $('.apf-news-pager .news h4').html(data.subheadline);
                $('.apf-news-pager .news p').html(data.content);
                APFNP.totalPages = data.newscount;
                APFNP.activate();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.apf-news-pager .news').html('<p class="error">' + APFNP.errorMessage + '</p>');
                APFNP.activate();
            }
        });
    };

    $('.apf-news-pager > .prev').click(function (event) {
        APFNP.getContent('prev');
        event.preventDefault();
    });
    $('.apf-news-pager > .next').click(function (event) {
        APFNP.getContent('next');
        event.preventDefault();
    });
});