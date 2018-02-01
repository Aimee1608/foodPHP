/**
 * layui 扩展公共模块
 * Autor: Fhua
 * Date: 16-11-25
 */

layui.define(['layer'], function (exports) {
    var $ = layui.jquery;

    var obj = {
        ajax: function (url, type, dataType, data, callback) {
            $.ajax({
                url: url,
                type: type,
                dataType: dataType,
                data: data,
                success: function (data, startic) {
                    if (data.state == 1) {
                        location.href = location.href;
                        obj.layerAlertS(data.msg, '提示');
                    }
                    else {
                        obj.layerAlertE(data.msg, '提示');
                    }
                },
                error: function () {

                }
            });
        },
        Ajaxpage:function(curr){

            var key=$('#key').val();
            $.getJSON(url, {
                page: curr || 1,key:key
            }, function(data){      //data是后台返回过来的JSON数据
                // console.log(data);die;
                $(".spiner-example").css('display','none'); //数据加载完关闭动画
                if(data==''){
                    $("#article_list").html('<td colspan="20" style="padding-top:10px;padding-bottom:10px;font-size:16px;text-align:center">暂无数据</td>');
                }else{
                    // article_list(data); //模板赋值
                    var tpl = document.getElementById('arlist').innerHTML;
                    laytpl(tpl).render(data, function(html){
                        document.getElementById('article_list').innerHTML = html;


                    });
                    laypage({
                        cont: $('#AjaxPage'),//容器。值支持id名、原生dom对象，jquery对象,
                        pages:allpages,//总页数
                        skip: true,//是否开启跳页
                        skin: '#1AB5B7',//分页组件颜色
                        curr: curr || 1,
                        groups: 5,//连续显示分页数
                        jump: function(ob, first){

                            if(!first){
                                obj.Ajaxpage(ob.curr)
                            }
                            $('#allpage').html('第'+ ob.curr +'页，共'+ ob.pages +'页');
                        }
                    });
                }
            });
        },
        layerDel: function (title, text, url, type, dataType, data, callback) {
            parent.layer.confirm(text, {
                title: title,
                btnAlign: 'c',
                resize: false,
                icon: 3,
                btn: ['确定删除', '容我想想'],
                yes: function () {
                    obj.ajax(url, type, dataType, data, callback);
                }
            });
        },
        //成功提示
        layerAlertS: function (text, title) {
            parent.layer.alert(text, { title: title, icon: 1, time: 5000, resize: false, zIndex: layer.zIndex, anim: Math.ceil(Math.random() * 6) });
        },
        //错误提示
        layerAlertE: function (text, title) {
            parent.layer.alert(text, { title: title, icon: 2, time: 5000, resize: false, zIndex: layer.zIndex, anim: Math.ceil(Math.random() * 6) });
        },
        //信息提示
        layerAlertI: function (text) {
            parent.layer.alert(text, { time: 5000, resize: false, zIndex: layer.zIndex, anim: Math.ceil(Math.random() * 6) });
            return;
        },
        layerPrompt: function () {
        },
        //询问层
        layerConfirm: function () {
        },
        //退出系统
        signOut: function (title, text, url,rturl,type, dataType, data, callback) {
            parent.layer.confirm(text, {
                title: title,
                resize: false,
                btn: ['确定退出', '容我想想'],
                btnAlign: 'c',
                icon: 3

            }, function () {
                $.ajax({
                    url: url,
                    type: type,
                    dataType: dataType,
                    data: data,
                    success: function (data, startic) {
                        if (data.code == 1) {
                            location.href = rturl;
                            obj.layerAlertS(data.message, '提示');
                        }
                        else {
                            obj.layerAlertE(data.message, '提示');
                        }
                    },
                    error: function () {

                    }
                });
            }, function () {
                layer.msg('也可以这样！', {
                    time: 20000, //20s后自动关闭
                    btnAlign: 'c',
                    btn: ['明白了']
                });
            });
        }

    }

    exports("common", obj);
});
