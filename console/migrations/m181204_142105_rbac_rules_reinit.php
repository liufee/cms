<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

/**
 * Class m181204_142105_rbac_rules_reinit
 */
class m181204_142105_rbac_rules_reinit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $this->delete( $authManager->assignmentTable );
        $this->delete( $authManager->itemChildTable );
        $this->delete( $authManager->itemTable );
        $this->alterColumn($authManager->itemTable, 'data', 'text');
        $this->batchInsert( $authManager->itemTable, ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'],
        [
            ['/ad/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"622","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"623","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"626","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"620","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"627","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"624","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"625","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"621","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/admin-user/create:GET', 2, '创建(查看)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"524","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/create:POST', 2, '创建(确定)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"525","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/delete:POST', 2, '删除', NULL, 's:69:"{"group":"\u6743\u9650","sort":"522","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/index:GET', 2, '列表', NULL, 's:69:"{"group":"\u6743\u9650","sort":"520","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/sort:POST', 2, '排序', NULL, 's:69:"{"group":"\u6743\u9650","sort":"523","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/update:GET', 2, '修改(查看)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"526","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/update:POST', 2, '修改(确定)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"527","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/view-layer:GET', 2, '查看', NULL, 's:69:"{"group":"\u6743\u9650","sort":"521","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/article/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"302","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"303","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"306","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"300","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"307","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"304","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"305","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"301","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/banner/banner-create:GET', 2, '创建(查看)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"611","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-delete:POST', 2, '删除(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"617","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-sort:POST', 2, '排序(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"616","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-update:GET', 2, '修改(查看)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"614","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-update:POST', 2, '修改(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"615","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-view-layer:GET', 2, '查看', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"613","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banners:GET', 2, '列表', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"610","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/create:GET', 2, '创建(查看)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"601","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/create:POST', 2, '创建(确定)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"602","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/delete:POST', 2, '删除', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"605","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/index:GET', 2, '列表', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"600","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/update:GET', 2, '修改(查看)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"603","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/category/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"312","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"313","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"316","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"310","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"317","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"314","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"315","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"311","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/clear/backend:GET', 2, '清除后台缓存', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"720","category":"\u7f13\u5b58"}";', 1543937188, 1543937188],
            ['/clear/frontend:GET', 2, '清除前台缓存', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"721","category":"\u7f13\u5b58"}";', 1543937188, 1543937188],
            ['/comment/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"324","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"320","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"322","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"323","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"321","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/friendly-link/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"702","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"703","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"706","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"700","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"707","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"704","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"705","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"701","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/frontend-menu/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"202","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"203","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"206","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"200","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"207","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"204","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"205","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"201","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/log/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"723","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/log/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"711","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/log/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"712","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/menu/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"212","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"213","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"216","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"210","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"217","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"214","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"215","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"211","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/page/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"332","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"333","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"336","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"330","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/sort:POST', 2, '排序  ', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"337","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"334","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"335","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"331","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/rbac/permission-create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"502","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"503","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u6743\u9650","sort":"507","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u6743\u9650","sort":"501","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"504","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"505","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u6743\u9650","sort":"506","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permissions:GET', 2, '列表', NULL, 's:63:"{"group":"\u6743\u9650","sort":"500","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/role-create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"511","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"512","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u6743\u9650","sort":"517","category":"\u89d2\u8272"}";', 1543937188, 1543937188],
            ['/rbac/role-update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"513","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"514","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u6743\u9650","sort":"515","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/roles-sort:POST', 2, '排序(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"516","category":"\u89d2\u8272"}";', 1543937188, 1543937188],
            ['/rbac/roles:GET', 2, '列表', NULL, 's:63:"{"group":"\u6743\u9650","sort":"510","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/setting/custom-create:GET', 2, '自定义设置创建(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"133","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-create:POST', 2, '自定义设置创建(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"134","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-delete:POST', 2, '删除', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"132","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-update:GET', 2, '自定义设置修改(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"135","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-update:POST', 2, '自定义设置修改(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"136","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom:GET', 2, '修改(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"130","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom:POST', 2, '修改(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"131","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/smtp:GET', 2, '修改(查看)', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"110","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/smtp:POST', 2, '修改(确定)', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"111","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/test-smtp:POST', 2, '测试stmp设置', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"112","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/website:GET', 2, '网站设置(查看)', NULL, 's:75:"{"group":"\u8bbe\u7f6e","sort":"100","category":"\u7f51\u7ad9\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/website:POST', 2, '网站设置(确定)', NULL, 's:75:"{"group":"\u8bbe\u7f6e","sort":"101","category":"\u7f51\u7ad9\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/user/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"402","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"403","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u7528\u6237","sort":"406","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u7528\u6237","sort":"400","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u7528\u6237","sort":"407","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"404","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"405","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u7528\u6237","sort":"401","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181204_142105_rbac_rules_reinit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181204_142105_rbac_rules_reinit cannot be reverted.\n",

        return false,
    }
    */

    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }
}
