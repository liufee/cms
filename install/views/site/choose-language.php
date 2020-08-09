<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

use yii\helpers\Url;

$this->title = "选择语言 Please choose language";
?>
<style>
    .main {
        text-align: center;
        background-color:;
    }
</style>
<div class="section">
    <div class="main">
        <select style="width: 350px" onchange="location.href=this.options[this.selectedIndex].value;">
        <option value="<?=Yii::$app->getRequest()->getHostInfo() . Yii::$app->getRequest()->getUrl()?>">请选择语言(Please choose language)</option>
        <?php
        foreach (Yii::$app->params['supportLanguages'] as $language => $languageDescription) {
            $url = Url::to(['site/language', 'lang' => $language]);
            echo "<option value='{$url}'>{$languageDescription}</option>";
        }
        ?>
        </select>
    </div>
</div>
