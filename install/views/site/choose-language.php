<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

use common\widgets\JsBlock;
use yii\helpers\Url;

$this->title = Yii::t("install", "Please chose language");
?>
<style>
    .main {
        text-align: center;
        background-color:;
    }
</style>
<div class="section">
    <div class="main">
        <select id="language" style="width: 350px">
        <option value="<?=Yii::$app->getRequest()->getHostInfo() . Yii::$app->getRequest()->getUrl()?>"><?=$this->title?></option>
        <?php
        foreach (Yii::$app->params['supportLanguages'] as $language => $languageDescription) {
            $selected = "";
            if (Yii::$app->language == $language) {
                $selected = "selected";
            }
            $url = Url::to(['site/language', 'lang' => $language]);
            echo "<option $selected value='{$url}'>{$languageDescription}</option>";
        }
        ?>
        </select>
        <div>
        <button id="next" class="btn btn-primary"><?= Yii::t('install', 'Next') ?></button>
        </div>
    </div>
</div>
<?php JsBlock::begin();?>
<script>
    $("#next").click(function () {
        location.href = $("#language").find("option:selected").val()
    })
</script>
<?php JsBlock::end();?>