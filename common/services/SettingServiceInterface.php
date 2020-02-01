<?php


namespace common\services;


interface SettingServiceInterface extends ServiceInterface
{
    const ServiceName = "settingService";

    public function updateWebsiteSetting(array $postData=[]);
    public function updateCustomSetting(array $postData=[]);
    public function updateSMTPSetting(array $postData = []);
    public function testSMTPSetting(array $postData = []);
}