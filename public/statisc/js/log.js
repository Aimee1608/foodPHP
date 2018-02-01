

layui.use(['layer', 'element', 'util', 'common', 'navbar', 'tab'], function () {
    var $ = layui.jquery
    , element = layui.element()
    , util = layui.util
    , common = layui.common;
    //新版功能
    $($('.logck')).on("click", function () {
        var $this = $(this);
        $($this).parent().parent(".admin-log-title").find(".admin-log-content").fadeToggle("slow");
    });

})
