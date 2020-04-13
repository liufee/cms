<?php
namespace backend\tests\model;
use backend\models\form\SettingSMTPForm;
use backend\tests\UnitTester;

class SettingSMTPFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function tryGetComponentConfig(UnitTester $I)
    {
        expect("get email component config", SettingSMTPForm::getComponentConfig())->hasKey('useFileTransport');
    }
}
