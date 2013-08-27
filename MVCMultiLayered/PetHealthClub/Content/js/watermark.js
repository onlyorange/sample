var ApplyWatermark = function (item, value) {
    $(item)
        .val(value)
        .addClass('watermark')
        .focus(function () {
            if ($(this).val() == value)
                $(this).val('');
            $(this).removeClass('watermark');
        })
        .blur(function () {
            if ($(this).val() == '') {
                $(this).val(value).addClass('watermark');
            }
        });
};

var ClearWatermark = function (item, value) {
    if ($(item).val() == value)
        $(item).val('');
}

//(function ($) {

//    $.fn.watermark = function (value) {
//        ApplyWatermark(this, value);
//    };

//})(jQuery);
