<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-08-07 23:15
 */

use yii\helpers\Url;

echo "<select onchange=\"location.href=this.options[this.selectedIndex].value;\">";
foreach (Yii::$app->params['supportLanguages'] as $language => $languageDescription) {
    $selected = "";
    if (Yii::$app->language == $language) {
        $selected = "selected";
    }
    $url = Url::to(['site/language', 'lang' => $language]);
    echo "<option $selected value='{$url}'>{$languageDescription}</option>";
}
echo "</select>";