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
                    layer.alert(jqXHR.responseJSON.message, {
                        title:tips.error,
                        btn: [tips.ok],
                        icon: 2,
                        skin: 'layer-ext-moon'
                    })
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
    layer.open({
        type: 2,
        title: obj.attr('title'),
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        area : ['800px' , '520px'],
        content: url
    });
}

$(document).ready(function(){
    //$('.info').animate({opacity: 1.0}, 3000).fadeOut('slow');
    $("input[type=file]").prettyFile({text:common.chooseFile});
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
        ids = ids.join(',');
        layer.confirm($(this).attr("data-confirm") + "<br>" + paramSign + ": " + ids, {
            title:tips.confirmTitle,
            btn: [tips.ok, tips.cancel] //按钮
        }, function() {//ok
            if( that.hasClass("jump") ){//含有jump的class不做ajax处理，跳转页面
                var jumpUrl = url.indexOf('?') !== -1 ? url + '&' + paramSign + '=' + ids : url + '?' + paramSign + '=' + ids;
                location.href = jumpUrl;
                return false;
            }
            var data = {};
            data[paramSign] = ids;
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
                    layer.alert(jqXHR.responseJSON.message, {
                        title:tips.error,
                        btn: [tips.ok],
                        icon: 2,
                        skin: 'layer-ext-moon'
                    })
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

    $('input[type=file]').bind('change', function () {
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

    $(".openContab").click(function(){
        parent.openConTab($(this));
        return false;
    });

    $("form:not(.none-loading)").bind("beforeSubmit", function () {
        $(this).find("button[type=submit]").attr("disabled", true);
        layer.load(2,{
            shade: [0.1,'#fff'] //0.1透明度的白色背景
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
            layer.alert(jqXHR.responseJSON.message, {
                title:tips.error,
                btn: [tips.ok],
                icon: 2,
                skin: 'layer-ext-moon'
            })
        },
        complete: function () {
            layer.closeAll('loading');
        }
    })
    return false;
}