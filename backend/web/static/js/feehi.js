yii.confirm = function(message, ok, cancel) {
    var url = $(this).attr('href');
    var if_pjax = $(this).attr('data-pjax') ? $(this).attr('data-pjax') : 0;
    var method = $(this).attr('data-method') ? $(this).attr('data-method') : "post";
    var data = $(this).attr('data-params') ? JSON.parse( $(this).attr('data-params') ) : '';
    layer.confirm(message, {
        title:tips.confirmTitle,
        btn: [tips.ok, tips.cancel] //按钮
    }, function(){//ok
        if( parseInt( if_pjax ) ){
            !ok || ok();
        }else {
            $.ajax({
                "url": url,
                "dataType": "json",
                "type": method,
                "data": data,
                beforeSend: function () {
                    layer.load(2,{
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                    });
                },
                "success": function (data) {
                    location.reload();
                },
                "error": function (jqXHR, textStatus, errorThrown) {
                    if( jqXHR.hasOwnProperty("responseJSON") ){
                        layer.alert(jqXHR.responseJSON.message, {
                            title:tips.error,
                            btn: [tips.ok],
                            icon: 2,
                            skin: 'layer-ext-moon'
                        })
                    }else{
                        layer.alert(jqXHR.responseText, {
                            title:tips.error,
                            btn: [tips.ok],
                            icon: 2,
                            skin: 'layer-ext-moon'
                        })
                    }
                },
                "complete": function () {
                    layer.closeAll('loading');
                }
            });
        }
    }, function(){//cancel
        !cancel || cancel();
    });
}
function viewLayer(url, obj)
{
    var area = ['80%', ($(window).height() - 100) + 'px'];
    if( isMobile || $(window).width() < 640) {
     area = ['100%', '100%']
    }
    layer.open({
     type: 2,
     title: obj.attr('title'),
     maxmin: true,
     shadeClose: true, //点击遮罩关闭层
     area: area,
     content: url
    });
}

function adaptPhone()
{
    var windowWidth = $(window).width();
    var tables = document.getElementsByTagName("table");
    if( tables.length <=0  ) return;
    var table = tables[0];
    var rows = table.rows;
    var columns = rows[0].cells.length;
    var displayColumns = 4;
    var lastColumnIndex = columns - 1;
    var i,j = 0;
    var display = "";
    if( columns > displayColumns ) {
        if(windowWidth < 640 || isMobile){
            display = "none";
        }
        for (i = 0; i < rows.length; i++) {
            for (j = displayColumns ; j < lastColumnIndex; j++) {
                if( !rows.hasOwnProperty(i) ) continue;
                if( !rows[i].cells.hasOwnProperty(j) ) continue;
                rows[i].cells[j].style.display = display;
            }

        }
    }
}

var isMobile = false;
var Agents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"];
for (var v = 0; v < Agents.length; v++) {
    if (navigator.userAgent.indexOf(Agents[v]) > 0) {
        isMobile = true;
        break;
    }
}
$(document).ready(function(){
    //$('.info').animate({opacity: 1.0}, 3000).fadeOut('slow');
    adaptPhone();
    $(window).resize(adaptPhone());
    //多选后处理
    $(".multi-operate").click(function () {
        var that = $(this);
        var url = $(this).attr('href');
        var method = $(this).attr('data-method') ? $(this).attr('data-method') : "post";
        var paramSign = that.attr('param-sign') ? that.attr('param-sign') : "id";
        var ids = [];
        $("tr td input[type=checkbox]:checked").each(function(){
            ids.push($(this).val());
        });
        if(ids.length <= 0){
            layer.alert(tips.noItemSelected, {
                title:tips.error,
                btn: [tips.ok],
                icon: 2,
                skin: 'layer-ext-moon'
            })
            return false;
        }
        layer.confirm($(this).attr("data-confirm") + "<br>" + paramSign + ": " + ids.join(","), {
            title:tips.confirmTitle,
            btn: [tips.ok, tips.cancel] //按钮
        }, function() {//ok
            if( that.hasClass("jump") ){//含有jump的class不做ajax处理，跳转页面
                var jumpUrl = url.indexOf('?') !== -1 ? url + '&' + paramSign + '=' + ids : url + '?' + paramSign + '=' + ids;
                location.href = jumpUrl;
                return false;
            }
            var data = {};
            data[paramSign] = JSON.stringify(ids);
            $.ajax({
                "url":url,
                "dataType" : "json",
                "type" : method,
                "data":data,
                beforeSend: function () {
                    layer.load(2,{
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                    });
                },
                "success" : function (data) {
                    location.reload();
                },
                "error": function (jqXHR, textStatus, errorThrown) {
                    if( jqXHR.hasOwnProperty("responseJSON") ) {
                        layer.alert(jqXHR.responseJSON.message, {
                            title: tips.error,
                            btn: [tips.ok],
                            icon: 2,
                            skin: 'layer-ext-moon'
                        })
                    }else{
                        layer.alert(jqXHR.responseText, {
                            title:tips.error,
                            btn: [tips.ok],
                            icon: 2,
                            skin: 'layer-ext-moon'
                        })
                    }
                },
                "complete": function () {
                    layer.closeAll('loading');
                }
            });
        }, function (index) {
            layer.close(index);
        })
        return false;
    })

    $("a.close-link").click(function () {
        var node = $(this).parents("div.ibox:first");
        node.hide();
        if(node.length == 0){
            $(this).parents("div.ibox-title").hide();
            $(this).parents("div.ibox-title:first").next().hide();
        }
        $(this).parents("div.ibox:first").hide();
    })

    $("a.collapse-link").click(function () {
        var node = $(this).parents("div.ibox:first").children("div.ibox-content");
        node.slideToggle();
        var iClass = $(this).children("i:first").attr('class');
        if(iClass == 'fa fa-chevron-up'){
            $(this).children("i:first").attr('class', 'fa fa-chevron-down');
        }else{
            $(this).children("i:first").attr('class', 'fa fa-chevron-up');
        }
        if(node.length == 0){
            $(this).parents("div.ibox-title:first").next().slideToggle();
        }

    })

    $('input.sort').blur(indexSort);

    $('a.refresh').click(function(){
        location.reload();
        return false;
    });

    //prettyFile文件选矿change后如果是图片显示图片
    $('input[type=file].pretty-file').bind('change', function () {
        if (typeof FileReader === 'undefined') {
            return;
        }
        var that = $(this);
        var files = $( this )[0].files;
        if(that.parent().parent().attr('class').indexOf("image") >= 0){
            if(!/image\/\w+/.test(files[0].type)){
                layer.tips(tips.onlyPictureCanBeSelected, that.parent().parent());
                return false;
            }
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = function (e) {
                if( that.parents("div.image").children().find('img').next().length >= 1 ){
                    that.parents("div.image").children().find('img').next().remove();
                }
                that.parents("div.image").children().find('img').attr("src", this.result).after('<div onclick="$(this).parents(\'.image\').find(\'input[type=hidden]\').val(0);$(this).prev().attr(\'src\', $(this).prev().attr(\'nonePicUrl\'));$(this).parents(\'.form-group\').find(\'input[type=file]\').val(\'\');$(this).remove();" style="position: absolute;width: 50px;padding: 5px 3px 3px 5px;top:5px;left:6px;background: black;opacity: 0.6;color: white;cursor: pointer"><i class="fa fa-trash" aria-hidden="true"> '+ common.deleteText +'</i></div>');
            }
        }
    });

    //在顶部导航栏打开tab
    $(".openContab").click(function(){
        parent.openConTab($(this));
        return false;
    });

    //提交表单后除form为none-loading的class显示loading效果
    $("form:not(.none-loading)").bind("beforeSubmit", function () {
        $(this).find("button[type=submit]").attr("disabled", true);
        layer.load(2,{
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
    })

    //prettyFile美化文件选框
    $("input[type=file].pretty-file").each(function () {
        $(this).prettyFile({
            text:this.getAttribute('text')
        });
    })

    //美化日期laydate插件
    lay('.date-time').each(function(){
        var config = {
            elem: this,
            type: this.getAttribute('dateType'),
            range: this.getAttribute('range') === 'true' ? true : ( this.getAttribute('range') === 'false' ? false : this.getAttribute('range') ),
            format: this.getAttribute('format'),
            value: this.getAttribute('val') === 'new Date()' ? new Date() : this.getAttribute('val'),
            isInitValue: this.getAttribute('isInitValue') != 'false',
            min: this.getAttribute('min'),
            max: this.getAttribute('max'),
            trigger: this.getAttribute('trigger'),
            show: this.getAttribute('show') != 'false',
            position: this.getAttribute('position'),
            zIndex: parseInt(this.getAttribute('zIndex')),
            showBottom: this.getAttribute('showBottom') != 'false',
            btns: this.getAttribute('btns').replace(/\[/ig, '').replace(/\]/ig, '').replace(/'/ig,'').replace(/\s/ig, '').split(','),
            lang: this.getAttribute('lang'),
            theme: this.getAttribute('theme'),
            calendar: this.getAttribute('calendar') != 'false',
            mark: JSON.parse(this.getAttribute('mark'))
        };

        if( !this.getAttribute('search') ){//搜素
            config.done = function(value, date, endDate){
                setTimeout(function(){
                    $(this).val(value);
                    var e = $.Event("keydown");
                    e.keyCode = 13;
                    $(".date-time[search!='true']").trigger(e);
                },100)
            }
        }
        delete config['val'];

        laydate.render(config);
    });

    //美化select选框jquery chosen
    $(".chosen-select").each(function(){
        $(this).chosen({
            allow_single_deselect: this.getAttribute('allow_single_deselect') !== 'false',
            disable_search: this.getAttribute('disable_search') !== 'false',
            disable_search_threshold: this.getAttribute('disable_search_threshold'),
            enable_split_word_search: this.getAttribute('enable_split_word_search') !== 'false',
            inherit_select_classes: this.getAttribute('inherit_select_classes') !== 'false',
            max_selected_options: this.getAttribute('max_selected_options'),
            no_results_text: this.getAttribute('no_results_text'),
            placeholder_text_multiple: this.getAttribute('placeholder_text_multiple'),
            placeholder_text_single: this.getAttribute('placeholder_text_single'),
            search_contains: this.getAttribute('search_contains') !== 'false',
            group_search: this.getAttribute('group_search') !== 'false',
            single_backstroke_delete: this.getAttribute('single_backstroke_delete') !== 'false',
            width: this.getAttribute('width'),
            display_disabled_options: this.getAttribute('display_disabled_options') !== 'false',
            display_selected_options: this.getAttribute('display_selected_options') !== 'false',
            include_group_label_in_selected: this.getAttribute('include_group_label_in_selected') !== 'false',
            max_shown_results: this.getAttribute('max_shown_results'),
            case_sensitive_search: this.getAttribute('case_sensitive_search') !== 'false',
            hide_results_on_select: this.getAttribute('hide_results_on_select') !== 'false',
            rtl: this.getAttribute('rtl') !== 'false'
        });
    })
})

function indexSort(){
    layer.load(2,{
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
    var data = {};
    var name = $(this).attr('name');
    data[name] = $(this).val();
    var sortHeader = $("th[sort-header=1]");
    $.ajax({
        url: sortHeader.attr('action'),
        method: sortHeader.attr('method'),
        data: data,
        error: function (jqXHR, textStatus, errorThrown) {
            if( jqXHR.hasOwnProperty("responseJSON") ) {
                layer.alert(jqXHR.responseJSON.message, {
                    title: tips.error,
                    btn: [tips.ok],
                    icon: 2,
                    skin: 'layer-ext-moon'
                })
            }else{
                layer.alert(jqXHR.responseText, {
                    title:tips.error,
                    btn: [tips.ok],
                    icon: 2,
                    skin: 'layer-ext-moon'
                })
            }
        },
        complete: function () {
            layer.closeAll('loading');
        }
    })
    return false;
}