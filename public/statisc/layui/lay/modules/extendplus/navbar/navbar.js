﻿/**
 * layui 扩展侧边菜单栏组件
 * Autor：masterbeginner / Fhua
 * Date : 16-11-26
 */

//配置navbar
//navbar.set({ elem: '#admin-navbar-side', url: 'datas/nav.json' });
//渲染navbar
//navbar.render();
//监听点击事件
//navbar.on('click(side)', function(data) { });

layui.define(['element', 'layer', 'common'], function (exports) {
    var $ = layui.jquery,
		layer = layui.layer,
		element = layui.element(),
		common = layui.common,
        cacheName = 'tb_navbar';

    var Navbar = function () {
        /**
		 *  默认配置 
		 */
        this.config = {
            elem: undefined, //容器
            data: undefined, //数据源
            url: undefined, //数据源地址
            type: 'GET', //读取方式
            cached: false //是否使用缓存
        };
        this.v = '0.0.1';
    };
    Navbar.prototype.render = function () {
        var _that = this;
        var _config = _that.config;       
        if (typeof (_config.elem) !== 'string' && typeof (_config.elem) !== 'object') {
            common.layerAlertE('Navbar error: elem参数未定义或设置出错.', '出错');
        }
        var $container;
        if (typeof (_config.elem) === 'string') {
            $container = $('' + _config.elem + '');
        }
        if (typeof (_config.elem) === 'object') {
            $container = _config.elem;
        }
        if ($container.length === 0) {
            common.layerAlertE('Navbar error:找不到elem参数配置的容器，请检查.', '出错');
        }
        if (_config.data === undefined && _config.url === undefined) {
            common.layerAlertE('Navbar error:请为Navbar配置数据源.', '出错')
        }
        if (_config.data !== undefined && typeof (_config.data) === 'object') {
            var html = getHtml(_config.data);
            $container.html(html);
            element.init();
            _that.config.elem = $container;
        } else {
            if (_config.cached) {
                var cacheNavbar = layui.data(cacheName);
                if (cacheNavbar.navbar === undefined) {
                    $.ajax({
                        type: _config.type,
                        url: _config.url,
                        async: false, //_config.async,

                        dataType: 'json',
                        success: function (result, status, xhr) {
                            //添加缓存
                            layui.data(cacheName, {
                                key: 'navbar',
                                value: result
                            });
                            var html = getHtml(result);
                            $container.html(html);
                            element.init();
                        },
                        error: function (xhr, status, error) {
                            common.msgError('Navbar error:' + error);
                        },
                        complete: function (xhr, status) {
                            _that.config.elem = $container;
                        }
                    });
                } else {
                    var html = getHtml(cacheNavbar.navbar);
                    $container.html(html);
                    element.init();
                    _that.config.elem = $container;
                }
            } else {
                //清空缓存
                layui.data(cacheName, null);
                $.ajax({
                    type: _config.type,
                    url: _config.url,
                    async: false, //_config.async,

                    dataType: 'json',
                    success: function (result, status, xhr) {
                        var html = getHtml(result);
                        $container.html(html);
                        element.init();
                    },
                    error: function (xhr, status, error) {
                        common.msgError('Navbar error:' + error);
                    },
                    complete: function (xhr, status) {
                        _that.config.elem = $container;
                    }
                });
            }
        }

        return _that;
    };
    /**
	 * 配置Navbar
	 * @param {Object} options
	 */
    Navbar.prototype.set = function (options) {
        var that = this; 
        //采用递归方式合并两个对象，并修改第一个对象。
        $.extend(that.config, options);
        return that;
    };

    Navbar.prototype.on = function (events, callback) {
        var that = this;
        var _con = that.config.elem;
        if (typeof (events) !== 'string') {
            common.layerAlertE('Navbar error:事件名配置出错.', '出错');
        }
        var lIndex = events.indexOf('(');
        var eventName = events.substr(0, lIndex);
        var filter = events.substring(lIndex + 1, events.indexOf(')'));
        if (eventName === 'click') {
            if (_con.attr('lay-filter') !== undefined) {
                _con.children('ul').find('li').each(function () {
                    var $this = $(this);
                    if ($this.find('dl').length > 0) {
                        var $dd = $this.find('dd').each(function () {
                            $(this).on('click', function () {
                                var $a = $(this).children('a');
                                var href = $a.data('url');
                                var icon = $a.children('i').attr('class');
                                var title = $a.children('cite').text();
                                var data = {
                                    elem: $a,
                                    field: {
                                        href: href,
                                        icon: icon,
                                        title: title
                                    }
                                }
                                callback(data);
                                //$(this).parent('dl').parent('li').addClass('layui-this').siblings().removeClass('layui-this');
                            });
                        });
                    } else {
                        $this.on('click', function () {
                            var $a = $this.children('a');
                            var href = $a.data('url');
                            var icon = $a.children('i').attr('class');
                            var title = $a.children('cite').text();
                            var data = {
                                elem: $a,
                                field: {
                                    href: href,
                                    icon: icon,
                                    title: title
                                }
                            }
                            callback(data);
                            //$this.addClass('layui-this').siblings().removeClass('layui-this');
                        });
                    }
                });
            }
        }
    };
    /**
	 * 获取html字符串
	 * @param {Object} data
	 */
    function getHtml(data) {
        var ulHtml = '<div id="sidebar" class="sidebar-fold"><i class="fa fa-bars"></i></div><ul class="layui-nav layui-nav-tree admin-nav-tree">';
        for (var i = 0; i < data.length; i++) {
            if (data[i].spread) {
                ulHtml += '<li class="layui-nav-item layui-nav-itemed">';
            } else {
                ulHtml += '<li class="layui-nav-item">';
            }
            if (data[i].children !== undefined && data[i].children.length > 0) {
                ulHtml += '<a href="javascript:;">';
                if (data[i].icon !== undefined && data[i].icon !== '') {
                    if (data[i].icon.indexOf('fa-') !== -1) {
                        ulHtml += '<i class="' + data[i].icon + '" aria-hidden="true"></i>';
                    } else {
                        ulHtml += '<i class="layui-icon">' + data[i].icon + '</i>';
                    }
                }
                ulHtml += '<cite>' + data[i].title + '</cite>'
                ulHtml += '</a>';
                ulHtml += '<dl class="layui-nav-child">'
                for (var j = 0; j < data[i].children.length; j++) {
                    ulHtml += '<dd>';
                    ulHtml += '<a href="javascript:;" data-url="' + data[i].children[j].href + '">';
                    if (data[i].children[j].icon !== undefined && data[i].children[j].icon !== '') {
                        if (data[i].children[j].icon.indexOf('fa-') !== -1) {
                            ulHtml += '<i class="' + data[i].children[j].icon + '" aria-hidden="true"></i>';
                        } else {
                            ulHtml += '<i class="layui-icon">' + data[i].children[j].icon + '</i>';
                        }
                    }
                    ulHtml += '<cite>' + data[i].children[j].title + '</cite>';
                    ulHtml += '</a>';
                    ulHtml += '</dd>';
                }
                ulHtml += '</dl>';
            } else {
                var dataUrl = (data[i].href !== undefined && data[i].href !== '') ? 'data-url="' + data[i].href + '"' : '';
                ulHtml += '<a href="javascript:;" ' + dataUrl + '>';
                if (data[i].icon !== undefined && data[i].icon !== '') {
                    if (data[i].icon.indexOf('fa-') !== -1) {
                        ulHtml += '<i class="' + data[i].icon + '" aria-hidden="true"></i>';
                    } else {
                        ulHtml += '<i class="layui-icon">' + data[i].icon + '</i>';
                    }
                }
                ulHtml += '<cite>' + data[i].title + '</cite>'
                ulHtml += '</a>';
            }
            ulHtml += '</li>';
        }
        ulHtml += '</ul>';

        return ulHtml;
    }

    var navbar = new Navbar();

    exports('navbar', function (options) {
        return navbar.set(options);
    });
    //exports('navbar', navbar);
});