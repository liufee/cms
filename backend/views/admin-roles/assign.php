<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:34
 */
use feehi\widgets\ActiveForm;
use feehi\widgets\JsBlock;

$this->title = "Assign Permission";
?>
<style>.hide{display: none}</style>
<div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
        <div class="ibox-content">
            <?php $form = ActiveForm::begin(); ?>
            <input type="button" name="selectAll" value="全选">
            <input type="button" name="selectCancelAll" value="全不选">
            <table>
                <?php
                $prevLevel = -1;
                foreach ($menus as $v){
                    $spaces = '';
                    $nextImg = '&nbsp;&nbsp;&nbsp;&nbsp;';
                    $checked = '';
                    foreach($model as $value){
                        if($value['menu_id'] == $v['id']) $checked = " checked='true' ";
                    }
                    foreach($menus as $value){
                        if($v['id'] == $value['parent_id']){
                            $nextImg = "<img class='expand-opened' src='static/img/toggle-collapse-dark.png'>";
                            break;
                        }
                    }
                    if($prevLevel == -1 || $v['level'] == 0){//第一个根菜单
                        echo "<tr class='level0'><td>{$nextImg}<input type='checkbox' name='permission[{$v['id']}]' {$checked} value='1'>{$v['name']}</td></tr>";
                    }else if($v['level'] >= $prevLevel){//二级三级菜单
                        $spaces = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $v['level']);
                        echo "<tr class='level{$v['level']} child' 'sub'='{$v['parent_id']}'><td>{$spaces}{$nextImg}<input type='checkbox' {$checked} name='permission[{$v['id']}]' value='1'>{$v['name']}</td></tr>";
                    }
                    //echo "<tr><td>".str_repeat("&nbsp;&nbsp;&nbsp;", $v['level']).Html::checkbox($v['name'])." {$v['name']}</td></tr>";
                    $prevLevel = $v['level'];
                }
                ?>
            </table>
            <div class="hr-line-dashed"></div>
            <?= $form->defaultButtons() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php JsBlock::begin() ?>
<script>
    $(document).ready(function(){
        $("table img").click(function(){
            var trClass = $(this).parent().parent().attr('class');
            var trClass = trClass.replace(/[^0-9]/g, '');
            var trClassNextLevel = parseInt(trClass)+1;
            if($(this).attr('class') == 'expand-closed') {
                $(this).parents('tr:first').nextUntil(".level0",".level"+trClassNextLevel).removeClass('hide');
                $(this).attr('src', 'static/img/toggle-collapse-dark.png');
                $(this).attr('class', 'expand-opened');
            }else{
                $(this).parents('tr:first').nextUntil(".level0", '.child').addClass('hide');
                $(this).attr('src', 'static/img/toggle-expand-dark.png');
                $(this).attr('class', 'expand-closed');
            }
        });

        $("table input").click(function(){
            if($(this).is(":checked")){
                var curVal = $(this).val();
                var nodes = $(this).parents("tr").siblings();//console.log(nodes);
                console.log(nodes[0].childNodes);
                nodes.each(function(i){
                   console.log(nodes[1].child);return false;
                });
            }else{

            }
        });
    })

    $("input[name=selectAll]").click(function(){
        $("input[type=checkbox]").each(function () {
            $(this).prop('checked', true);
        });
    })
    $("input[name=selectCancelAll]").click(function(){
        $("input[type=checkbox]").each(function () {
            $(this).prop('checked', false);
        });
    })
</script>
<?php JsBlock::end() ?>