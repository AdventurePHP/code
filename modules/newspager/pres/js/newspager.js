$(document).ready(function () {
    var APFNP = {};
    APFNP.page = 1;
    APFNP.totalPages = <html:placeholder name="NewsCount" />;
    APFNP.getUrl = function (page) {
        return '<html:placeholder name="NewsServiceBaseURL" />' + page
            + '<html:placeholder name="NewsServiceLangParam" />'
            + '<html:placeholder name="NewsLanguage" />'
            + '<html:placeholder name="NewsServiceDataDir" />';
    };
    APFNP.getContent = function (direction) {
        if (direction == 'prev') {
            APFNP.page--;
            if (APFNP.page == 0) {
                APFNP.page = 1;
                return;
            }
        } else {
            APFNP.page++;
            if (APFNP.page > APFNP.totalPages) {
                APFNP.page = APFNP.totalPages;
                return;
            }
        }
        $.ajax({
            dataType: 'xml',
            cache: false,
            url: APFNP.getUrl(APFNP.page),
            success: function (data, textStatus, jqXHR) {
                $('.apf-news-pager .news h3').html($(data).find('news headline').text());
                $('.apf-news-pager .news h4').html($(data).find('news subheadline').text());
                $('.apf-news-pager .news p').html($(data).find('news content').text());
                APFNP.totalPages = parseInt($(data).find('news newscount').text());
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.apf-news-pager .news').html('<p class="error"><html:placeholder name="ErrorMsg" /></p>');
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