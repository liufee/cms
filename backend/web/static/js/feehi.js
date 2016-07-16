yii.confirm = function(message, ok, cancel) {
    swal({
        title: deleteTips.realyToDelete,
        text: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: deleteTips.surelyDeleteItem,
        closeOnConfirm: false
    }, function (isConfirm) {
        if(isConfirm) {
            ok();
            swal(deleteTips.successDelete, deleteTips.successDeleted, "success");
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
    $("input[type=file]").prettyFile();
    $(".multi-delete").click(function () {
        var url = $(this).attr('href');
        var ids = new Array();
        $("tr td input[type=checkbox]:checked").each(function(){
            ids.push($(this).val());
        });
        if(ids.length <= 0){
            swal(deleteTips.noItemSelected, deleteTips.PleaseSelectOne);
            return false;
        }
        ids = ids.join(',');
        swal({
            title: deleteTips.realyToDelete,
            text: deleteTips.surelyDeleteItems+ids+"?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: deleteTips.deleteButton,
            closeOnConfirm: false
        }, function (isConfirm) {
            if(isConfirm) {
                swal(deleteTips.deleteWithNoRefresh, deleteTips.deleting+'...', "success");
                $.ajax({
                    'url':url,
                    'method':'get',
                    'data':{'id':ids},
                    'success':function (data) {
                        if(data.code) {
                            swal(deleteTips.deleteSuccess+'!', deleteTips.successDeleted+'.', "success");
                            location.reload();
                        }else {
                            swal(data.msg+deleteTips.deleteFailed+'!', deleteTips.failedDelete+'.', "error");
                        }
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

    $('a.sort').click(function(){
        $('input[type=number]').each(function(){
            $(this).clone().attr('type', 'hidden').appendTo('form[name=sort]');
            $('form[name=sort]').append();
        })
        $('form[name=sort]').submit();
        return false;
    })

    $('a.refresh').click(function(){
        location.reload();
        return false;
    });
    $('input[type=file]').bind('change', function () {
        alert(222);
    })
})