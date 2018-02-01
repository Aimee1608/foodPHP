/**
 * 内容列表js
 * Autor: Fhua
 * Date: 16-11-29
 */

layui.use(['layer', 'laypage', 'common', 'icheck'], function () {
    var $ = layui.jquery
    , layer = layui.layer
    , laypage = layui.laypage
    , common = layui.common;

    //加载单选框样式
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    //表格行点击勾选
    $('.layui-table tbody tr').on('click', function () {
        var $this = $(this);
        var $input = $this.children('td').eq(0).find('input');
        $input.on('ifChecked', function (e) {
            $this.css('background-color', '#EEEEEE');
        });
        $input.on('ifUnchecked', function (e) {
            $this.removeAttr('style');
        });
        $input.iCheck('toggle');
    }).find('input').each(function () {
        var $this = $(this);
        $this.on('ifChecked', function (e) {
            $this.parents('tr').css('background-color', '#EEEEEE');
        });
        $this.on('ifUnchecked', function (e) {
            $this.parents('tr').removeAttr('style');
        });
    });
    //全选
    $('#selected-all').on('ifChanged', function (event) {
        var $input = $('.layui-table tbody tr td').find('input');
        $input.iCheck(event.currentTarget.checked ? 'check' : 'uncheck');
    });

    var active = {
        doAdd: function () {
            var url = $(this).data('href');
            if (url) {
                window.location.href = url;
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        },
        doEdit: function () {
            var url = $(this).data('href');
            if (url) {
                window.location.href = url;
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        },
        doDelete: function () {
            var url = $(this).data('href');
            if (url) {
                //查出选择的记录
                if ($(".layui-table tbody input:checked").size() < 1) {
                    common.layerAlertE('对不起，请选中您要操作的记录！', '提示');
                    return false;
                }
                var ids = "";
                var checkObj = $(".layui-table tbody input:checked");
                for (var i = 0; i < checkObj.length; i++) {
                    if (checkObj[i].checked && $(checkObj[i]).attr("disabled") != "disabled")
                        ids += $(checkObj[i]).attr("ids") + ','; //如果选中，将value添加到变量idlist中    
                }
                var data = { "ids": ids };
                common.layerDel('确认删除这些信息？', '此操作不可逆，请再次确认是否要操作。', url, 'post', 'json', data);
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        },
        doBatchDelete: function () {
            var url = $(this).data('href');
            if (url) {
                var data = { "ids": "" };
                common.layerDel('确认删除这些信息？', '此操作不可逆，请再次确认是否要操作。', url, 'post', 'json', data);
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        },
        doDbBak: function () {
            var url = $(this).data('href');
            if (url) {
                common.ajax(url, 'post', 'json', '');
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        }
    };

    $('.do-action').on('click', function (e) {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
        layui.stope(e);
    });


});