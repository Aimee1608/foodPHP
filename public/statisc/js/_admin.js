// JavaScript Document

//                            _ooOoo_

//                           o8888888o

//                           88" . "88

//                           (| -_- |)

//                            O\ = /O

//                        ____/`---'\____

//                      .   ' \| |// `.

//                       / \||| : |||// \

//                     / _||||| -:- |||||- \

//                       | | \\ - /// | |

//                     | \_| ''\---/'' | |

//                      \ .-\__ `-` ___/-. /

//                   ___`. .' /--.--\ `. . __

//                ."" '< `.___\_<|>_/___.' >'"".

//               | | : `- \`.;`\ _ /`;.`/ - ` : | |

//                 \ \ `-. \_ __\ /__ _/ .-` / /

//         ======`-.____`-.___\_____/___.-`____.-'======

//                            `=---='

//         .............................................

//                  佛祖保佑             永无BUG
layui.use(['layer', 'element', 'util', 'common', 'navbar', 'tab'], function () {
    var $ = layui.jquery
        , element = layui.element()
        , util = layui.util
        , common = layui.common
        , navbar = layui.navbar()
        , tab = layui.tab({
        elem: '.layui-tab-card' //设置选项卡容器

    });

    //iframe自适应
    $(window).on('resize', function () {
        var $content = $('.admin-nav-card .layui-tab-content');
        $content.height($(this).height() - 192);
        $content.find('iframe').each(function () {
            $(this).height($content.height());
        });
    }).resize();
    $.getJSON('/FlowProject/Food_test/public/index.php/admin/ajax/getMenuList',{},function(data){
        navbar.set({
            elem: '#admin-navbar-side',
            data: data
        });
        navbar.render();
        navbar.on('click(side)', function(data) {
            tab.tabAdd(data.field);
        });
    });

    /* //模拟点击内容管理
     $('#menu').find('a[data-fid=85]').click();*/

    //固定Bar
    util.fixbar({
        bar1: true
        , click: function (type) {
            if (type === 'bar1') {
                location.href = 'http://nnfhua.com';
            }
        }
    });

    //退出系统
    var adminActive = {
        doLoginOut: function () {
            var url = $(this).data('href');
            var rturl = $(this).data('rturl');
            if (url) {
                if (!rturl) {
                    rturl = '/Login/Login';
                }
                common.signOut('确认退出系统？', '请再次确认是否要退出系统！', url, rturl, 'post', 'json', {});
            }
            else {
                common.layerAlertE('链接错误！', '提示');
            }
        }
    };

    $('.do-admin').on('click', function (event) {
        var type = $(this).data('type');
        adminActive[type] ? adminActive[type].call(this) : '';
        return false;
    });

    //左侧菜单收缩
    var foldNode = $('#sidebar');
    var sidebarNode = $('#sidebar-side');
    var headerNode = $('.header-admin');
    if (foldNode) {
        $(document).on("click", '#sidebar', function () {
            var toType = sidebarNode.hasClass("sidebar-mini") ? "full" : "mini";
            var sideWidth = sidebarNode.width();
            if (sideWidth === 200) {
                $('#admin-body').animate({
                    left: '70px'
                }); //admin-footer
                $('.admin-footer').animate({
                    left: '70px'
                });
                sidebarNode.addClass('sidebar-mini');
                headerNode.addClass('header-mini');
                $('#sidebar').find('i').removeClass('fa-bars').addClass('fa-th-large');
            } else {
                $('#admin-body').animate({
                    left: '200px'
                });
                $('.admin-footer').animate({
                    left: '200px'
                });
                sidebarNode.removeClass('sidebar-mini');
                headerNode.removeClass('header-mini');
                $('#sidebar').find('i').removeClass('fa-th-large').addClass('fa-bars');
            }
        });
    }
})
