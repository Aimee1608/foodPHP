/**
 * 新增编辑js
 * Autor: Fhua
 * Date: 16-12-15
 */

layui.use(['layer', 'laypage', 'common', 'element', 'form', 'upload', 'laydate'], function () {
    var $ = layui.jquery
        , layer = layui.layer
        , laypage = layui.laypage
        , common = layui.common
        , form = layui.form()
        , laydate = layui.laydate
        , element = layui.element;

    //多项选项PROP
    var active = {
        ruleMultiPorp: function () {
            var parentObj = $(".rule-multi-porp");
            $(parentObj).each(function () {
                var $parentObj = $(this);
                $parentObj.addClass("multi-porp"); //添加样式
                $parentObj.children().hide(); //隐藏内容
                var divObj = $('<ul></ul>').prependTo($parentObj); //前插入一个DIV
                $parentObj.find(":checkbox").each(function () {
                    var indexNum = $parentObj.find(":checkbox").index(this); //当前索引
                    var liObj = $('<li></li>').appendTo(divObj)
                    var newObj = $('<a href="javascript:;">' + $parentObj.find('label').eq(indexNum).text() + '</a><i></i>').appendTo(liObj); //查找对应Label创建选项
                    if ($(this).prop("checked") == true) {
                        liObj.addClass("selected"); //默认选中
                    }
                    //检查控件是否启用
                    if ($(this).prop("disabled") == true) {
                        newObj.css("cursor", "default");
                        return;
                    }
                    //绑定事件
                    $(newObj).click(function () {
                        if ($(this).parent().hasClass("selected")) {
                            $(this).parent().removeClass("selected");
                        } else {
                            $(this).parent().addClass("selected");
                        }
                        $parentObj.find(':checkbox').eq(indexNum).trigger("click"); //触发对应的checkbox的click事件
                        //alert(parentObj.find(':checkbox').eq(indexNum).prop("checked"));
                    });
                });
            });
        },
        submit: function (dat) {
            var url = $(this).data('href');
            if (url) {
                $.ajax({
                    url: url,
                    type: type,
                    dataType: dataType,
                    data: data,
                    success: function (data, startic) {
                        if (data.state == 1) {
                            location.href = location.href;
                            obj.layerAlertS(data.message, '提示');
                        }
                        else {
                            obj.layerAlertE(data.message, '提示');
                        }
                    },
                    error: function () {

                    }
                });
            } else {
                common.layerAlertE('链接错误！', '提示');
            }
        },
    };
    //初始化PROP
    active['ruleMultiPorp'] ? active['ruleMultiPorp'].call(this) : '';

    form.on('submit(demo1)', function (data) {
        console.log(JSON.stringify(data.field));
        var url = $(this).data('href');
        var rturl = $(this).data('rturl');
        if (url) {
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data.field,
                success: function (data, startic) {
                    if (data.state == 1) {
                        location.href = rturl;
                        common.layerAlertS(data.message, '提示');
                    }
                    else {
                        common.layerAlertE(data.message, '提示');
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    common.layerAlertE(textStatus, '提示');
                }
            });
        } else {
            common.layerAlertE('链接错误！', '提示');
        }
        return false;
    });

});