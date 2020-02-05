<?php

namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class RbacTest
 */
class RbacCest
{

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkPermissions(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $I->see('路由');
        $I->see("描述");
    }

    public function checkCreatePermission(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permission-create'));
        $I->fillField("RBACPermissionForm[route]", '/test/index');
        $I->fillField("RBACPermissionForm[description]", 'test permission description');
        $I->fillField("RBACPermissionForm[group]", 'test group');
        $I->fillField("RBACPermissionForm[category]", 'test category');
        $I->submitForm("button[type=submit]", ["RBACPermissionForm[method]" => "POST"]);
        $I->see("/test/index");
    }

    public function checkUpdatePermission(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $I->click("a[title=编辑]");
        $I->fillField("RBACPermissionForm[route]", '/test/index-update');
        $I->fillField("RBACPermissionForm[description]", 'test update permission description');
        $I->fillField("RBACPermissionForm[group]", 'test update group');
        $I->fillField("RBACPermissionForm[category]", 'test update category');
        $I->submitForm("button[type=submit]", ["RBACPermissionForm[method]" => "GET"]);
        $I->see("/test/index-update");
    }

    public function checkViewPermission(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('路由');
    }

    public function checkDeletePermission(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $urls = $I->grabMultiple("table a[title=删除]", "href");
        $I->sendAjaxPostRequest($urls[0]);
        $I->see('success');
    }

    public function checkSortPermission(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = json_encode(["name" => $data['name']]);
        $I->sendAjaxPostRequest(Url::toRoute("/rbac/permission-sort"), [
            "sort[$key]" => 1,
        ]);
        $I->see('success');
    }

    public function checkRoles(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $I->see('角色');
        $I->see("描述");
    }

    public function checkCreateRole(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/role-create'));
        $I->fillField("RBACRoleForm[name]", 'test role');
        $I->fillField("RBACRoleForm[description]", 'test role description');
        $I->submitForm("button[type=submit]", []);
        $I->see("test role");
    }

    public function checkUpdateRole(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/role-create'));
        $I->fillField("RBACRoleForm[name]", 'test role');
        $I->fillField("RBACRoleForm[description]", 'test role description');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $I->click("a[title=编辑]");
        $I->fillField("RBACRoleForm[name]", 'test update role');
        $I->fillField("RBACRoleForm[description]", 'test update role');
        $I->submitForm("button[type=submit]", []);
        $I->see("test update role");
    }

    public function checkViewRole(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/role-create'));
        $I->fillField("RBACRoleForm[name]", 'test role');
        $I->fillField("RBACRoleForm[description]", 'test role description');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('角色');
    }

    public function checkDeleteRole(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/role-create'));
        $I->fillField("RBACRoleForm[name]", 'test role');
        $I->fillField("RBACRoleForm[description]", 'test role description');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $urls = $I->grabMultiple("table a[title=删除]", "href");
        $I->sendAjaxPostRequest($urls[0]);
        $I->see('success');
    }

    public function checkSortRole(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/role-create'));
        $I->fillField("RBACRoleForm[name]", 'test role');
        $I->fillField("RBACRoleForm[description]", 'test role description');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = json_encode(["name" => $data['name']]);
        $I->sendAjaxPostRequest(Url::toRoute("/rbac/role-sort"), [
            "sort[$key]" => 1,
        ]);
        $I->see('success');
    }
}
