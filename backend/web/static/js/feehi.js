yii.confirm = function(message, ok, cancel) {
    var url = $(this).attr('href');
    var if_pjax = $(this).attr('data-pjax') ? $(this).attr('data-pjax') : 0;
    var method = $(this).attr('data-method') ? $(this).attr('data-method') : "post";
    var data = $(this).attr('data-params') ? JSON.parse( $(this).attr('data-params') ) : '';
    swal({
        title: message,
        text: tips.realyToDo,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        cancelButtonText: tips.cancel,
        confirmButtonText: tips.ok,
        closeOnConfirm: false
    }, function (isConfirm) {
        if(isConfirm) {
            if( parseInt( if_pjax ) ){
                !ok || ok();
            }else {
                swal(tips.waitingAndNoRefresh, tips.operating + '...', "success");
                $.ajax({
                    "url": url,
                    "dataType": "json",
                    "type": method,
                    "data": data,
                    "success": function (data) {
                        swal(tips.success + '!', tips.operatingSuccess + '.', "success");
                        location.reload();
                    },
                    "error": function (jqXHR, textStatus, errorThrown) {
                        swal(tips.error + ': ' + jqXHR.responseJSON.message, tips.operatingFailed + '.', "error");
                    }
                });
            }
        }else{
            !cancel || cancel();
        }
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
        var ids = new Array();
        $("tr td input[type=checkbox]:checked").each(function(){
            ids.push($(this).val());
        });
        if(ids.length <= 0){
            swal(tips.noItemSelected, tips.PleaseSelectOne);
            return false;
        }
        ids = ids.join(',');
        swal({
            title: $(this).attr("data-confirm"),
            text: ids,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: tips.cancel,
            confirmButtonText: tips.ok,
            closeOnConfirm: false
        }, function (isConfirm) {
            if(isConfirm) {
                swal(tips.waitingAndNoRefresh, tips.operating+'...', "success");
                if( that.hasClass("jump") ){//含有jump的class不做ajax处理，跳转页面
                    var jumpUrl = url.indexOf('?') !== -1 ? url + '&' + paramSign + '=' + ids : url + '?' + paramSign + '=' + ids;
                    location.href = jumpUrl;
                    return;
                }
                var data = {};
                data[paramSign] = ids;
                $.ajax({
                    "url":url,
                    "dataType" : "json",
                    "type" : method,
                    "data":data,
                    "success" : function (data) {
                        swal(tips.success + '!', tips.operatingSuccess + '.', "success");
                        location.reload();
                    },
                    "error": function (jqXHR, textStatus, errorThrown) {
                        swal(tips.error + ': ' + jqXHR.responseJSON.message, tips.operatingFailed + '.', "error");
                    }
                });
            }else{
                return false;
            }
        });
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
        layer.load(2);
    })
})

function indexSort(){
    layer.load(2);
    var data = {};
    var name = $(this).attr('name');
    data[name] = $(this).val();
    var sortHeader = $("th[sort-header=1]");
    $.ajax({
        url: sortHeader.attr('action'),
        method: sortHeader.attr('method'),
        data: data,
        error: function (jqXHR, textStatus, errorThrown) {
            swal(tips.error + ': ' + jqXHR.responseJSON.message, tips.operatingFailed + '.', "error");
        },
        complete: function () {
            layer.closeAll('loading');
        }
    })
    return false;
}