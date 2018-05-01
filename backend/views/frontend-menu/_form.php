<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:35
 */

/**
 * @var $this yii\web\View
 * @var $model frontend\models\Menu
 */

use backend\widgets\ActiveForm;
use common\helpers\FamilyTree;
use common\libs\Constants;
use common\models\Category;
use common\widgets\JsBlock;
use frontend\models\Menu;
use yii\helpers\ArrayHelper;

$this->title = "Frontend Menus";
$parent_id = yii::$app->getRequest()->get('parent_id', '');
if ($parent_id != '') {
    $model->parent_id = $parent_id;
}
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                $disabledOptions = [];
                if(!$model->getIsNewRecord()){
                    $disabledOptions[$model->id] = ['disabled' => true];
                    $familyTree = new FamilyTree(Menu::getMenus(Menu::FRONTEND_TYPE));
                    $descendants = $familyTree->getDescendants($model->id);
                    $descendants = ArrayHelper::getColumn($descendants, 'id');
                    foreach ($descendants as $descendant){
                        $disabledOptions[$descendant] = ['disabled' => true];
                    }
                }
                ?>
                <?= $form->field($model, 'parent_id')->label(yii::t('app', 'Parent Menu Name'))->dropDownList(Menu::getMenusName(Menu::FRONTEND_TYPE), ['options' => $disabledOptions]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_absolute_url')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'url', ['template'=>'{label}<div class="col-sm-{size}"><input name="urlType" checked value="new" type="radio">' . yii::t('app', 'Input new') . ' &nbsp;&nbsp;<input value="select" name="urlType" type="radio">' . yii::t('app', 'Select from article category') . '<div class="form-group field-menu-url required">{input}</div>{error}</div>{hint}'])->textInput()?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'target')->radioList(Constants::getTargetOpenMethod()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_display')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php JsBlock::begin() ?>
<script>
    String.prototype.trim = function (char, type) {
        if (char) {
            if (type == 'left') {
                return this.replace(new RegExp('^\\'+char+'+', 'g'), '');
            } else if (type == 'right') {
                return this.replace(new RegExp('\\'+char+'+$', 'g'), '');
            }
            return this.replace(new RegExp('^\\'+char+'+|\\'+char+'+$', 'g'), '');
        }
        return this.replace(/^\s+|\s+$/g, '');
    };
    $(document).ready(function () {
        var urlType = $("input[name=urlType]");
        var categoryUrl =
        <?php
            $menuCategories = Category::getMenuCategories();
            if($model->id){
                foreach ($menuCategories as $k => $menuCategory){
                    if($k == $model->url){
                        echo "'" . $k ."';";
                        break;
                    }
                }
                echo "'';";
            }else{
                echo "'';";
            }
        ?>
        if( categoryUrl != '' ){
            $("input[value=new]").attr('checked', false);
            $("input[value=select]").attr('checked', true);
            var input = '<?= str_replace("\n", '', $form->field($model, 'url', ['template' => '{input}'])
                ->label(false)
                ->dropDownList($menuCategories)) ?>';
            urlType.parent().children("div.field-menu-url").remove();
            urlType.parent().append(input);
            $("select[id=menu-url]").bind('change', changeCategoryMenu);
        }
        urlType.change(function () {
            var val = $(this).val();
            if (val == 'select') {
                var input = '<?= str_replace("\n", '', $form->field($model, 'url', ['template' => '{input}'])
                    ->label(false)
                    ->dropDownList($menuCategories)) ?>';
            } else {
                var input = '<?= str_replace("\n", '', $form->field($model, 'url', ['template' => '{input}'])
                    ->label(false)
                    ->textInput())?>';
            }
            $(this).parent().children("div.field-menu-url").remove();
            $(this).parent().append(input);
            if(val == 'select'){
                $("select[id=menu-url]").bind('change', changeCategoryMenu);
            }
        })
    })
    function changeCategoryMenu()
    {
        $("input[id=menu-name]").val( $("select[id=menu-url] :selected").html().trim(' │', 'left').trim(' ├', 'left').trim(' └', 'left').trim('-', 'left') );
    }
</script>
<?php JsBlock::end() ?>