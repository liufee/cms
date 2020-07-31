<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-20 11:49
 */

return [
    'Install guide' => '安装向导',
    'license agreement' => '许可协议',
    'Accept' => '接受',
    'Decline' => '拒绝',
    'Check Environment' => '检查环境',
    'Create Data' => '填写信息',
    'Success' => '完成安装',
    'Environment' => '环境检测',
    'Recommends' => '推荐配置',
    'Least Required' => '最低要求',
    'OS' => '操作系统',
    'Current' => '当前状态',
    'Unix-like' => '类Unix',
    'Unrestricted' => '不限',
    'Required' => '必须',
    'Yes' => '是',
    'No' => '否',
    'Module' => '模块',
    'Directory File Permission' => '文件文件夹权限',
    'Writtable' => '可写',
    'Readable' => '可读',
    'Other' => '其他',
    'Upload Limit' => '上传限制',
    'Refresh' => '重新检测',
    'Next' => '下一步',
    'Forbidden' => '禁止上传',
    'Database' => '数据库配置',
    'Type' => '数据库类型',
    'Recommend MySQL' => '推荐MySQL',
    'DB Host' => '数据库地址',
    'DB Port' => '数据库端口',
    'DB Username' => '数据库用户名',
    'DB Password' => '数据库密码',
    'Table Prefix' => '表前缀',
    'DB Name' => '数据库名',
    'Website' => '网站配置',
    'Title' => '网站标题',
    'Site Url' => '网站地址',
    'Keywords' => '网站关键词',
    'Description' => '网站描述',
    'Administrator' => '管理员配置',
    'Username' => '用户名',
    'Password' => '密码',
    'Re-password' => '重复密码',
    'Email' => '邮箱',
    'Prev' => '上一步',
    'Install' => '安装',
    '{attribute} cannot be empty' => "{attribute}不能为空",
    'Database Username' => '数据库用户名',
    'Database Password' => '数据库密码',
    'Database Name' => '数据库名',
    'Admin Username' => '管理员用户名',
    'Only in one database install various cms should update' => '若一个数据库安装多个cms时以区分不同cms',
    'Database host, localhost is the common' => '数据库地址一般为localhost',
    'Default mysql 3306, PostgreSQL 5432' => '一般MySQL 3306, PostgreSQL 5432',
    'Please end at "/"' => '请以 "/" 结尾',
    'Admin Password' => '密码',
    'Repeat Password' => '重复密码',
    'Super administrator, own the whole permission' => '超级管理员，拥有所有权限',
    'Verifing, no refresh this window.' => '正在验证配置，请勿刷新本页',
    'Cannot find database host' => '数据库地址找不到',
    'Repeat password is not equal password' => '两次密码输入的不一致',
    'Access to database `{database}` error.Maybe permission denied' => '使用数据库发生错误，可能该账号没有使用{database}数据库的权限',
    'Create database `{database}` success.But no permission to use it' => '创建数据库{database}成功，但是此账号没有使用{database}数据库的权限',
    'For your site security, please remove the directory install! and, backup common/config/conf/db.php' => '为了您站点的安全，安装完成后即可将网站目录下的“install”文件夹删除!另请对common/config/conf/db.php文件做好备份，以防丢失！',
    'Congratuations! Success installed' => '恭喜您,安装成功!',
    'Create database error, please create yourself and retry' => '创建数据库失败,请手动创建后再试',
    'Please check your environment to suite the cms' => '最低要求环境不满足',
    ' If environment have been suit to the cms please check php session can set correctly' => '如果环境已满足仍提示此错误，请检查是否php的session是否能够正确设置',
    'Create table {table} finished' => '创建表{table}完成',
    'Create table {table} index {index} finished' => '创建表{table}索引{index}完成 ',
    'Insert table {table} data finished' => '导入表{table}数据完成',
    'finished' => '完成',
    'error' => '失败',
    'go Frontend' => '进入前台',
    'go Backend' => '进入后台',
    'Has been installed, if you want to reinstall please remove ' => '已经安装过了，如果想要重新安装请删除 ',
    ' and try it again' => '并重新运行',
    'Finish Install' => '完成安装',
    'Installing' => '正在安装',
    'Installed success;but update write config file error.please update common/config/main-local.php components db section.' => "安装成功，修改配置文件common/config/main-local.php失败,请手动修改components db数据库配置",
    "Touch install lock file " . \install\controllers\SiteController::$installLockFile . " failed,please touch file handled" => "创建安装锁文件" . \install\controllers\SiteController::$installLockFile . "失败,请手动创建",
];