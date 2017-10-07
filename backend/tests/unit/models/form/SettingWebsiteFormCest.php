<?php
namespace backend\tests\models;
use backend\models\form\SettingWebsiteForm;
use backend\tests\UnitTester;

class SettingWebsiteFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function tryGetWebsiteSetting(UnitTester $I)
    {
        $model = new SettingWebsiteForm();
        $model->getWebsiteSetting();
        expect("result should have attribute website_title", $model)->hasAttribute('website_title');
    }
}
