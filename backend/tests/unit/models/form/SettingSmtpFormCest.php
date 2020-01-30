<?php
namespace backend\tests\model;
use backend\models\form\SettingSMTPForm;
use backend\tests\UnitTester;

class SettingSmtpFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function tryGetSmtpConfig(UnitTester $I)
    {
        $model = new SettingSMTPForm();
        $model->getSmtpConfig();
        expect("result should have attribute smtp_port", $model)->hasAttribute('smtp_port');
    }

    public function tryGetComponentConfig(UnitTester $I)
    {
        expect("get email component config", SettingSMTPForm::getComponentConfig())->hasKey('useFileTransport');
    }
}
