<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 17:18
 */

namespace common\services;


interface RBACServiceInterface extends ServiceInterface
{
    const ServiceName = "RBACService";

    public function getNewPermissionModel();
    public function getPermissionList(array $query = []);
    public function createPermission(array $postData = []);
    public function getPermissionDetail($name);
    public function updatePermission($name, array $postData = []);
    public function deletePermission($name);

    public function getNewRoleModel();
    public function getRoleList(array $query = []);
    public function createRole(array $postData = []);
    public function getRoleDetail($name);
    public function updateRole($name, array $postData = []);
    public function sortRole($name, $sort);
    public function deleteRole($name);
    public function getPermissionsGroups();
    public function getPermissionGroups();
    public function getPermissionCategories();
    public function getRoles();
}