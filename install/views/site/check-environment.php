<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-19 12:29
 */

use yii\helpers\Url;

$this->title = yii::t('install', 'Environment');
?>
<section class="section">
    <?= $this->render('_steps') ?>
    <div class="server">
        <table width="100%">
            <tr>
                <td class="td1"><?= yii::t('install', 'Environment') ?></td>
                <td class="td1" width="25%"><?= yii::t('install', 'Recommends') ?></td>
                <td class="td1" width="25%"><?= yii::t('install', 'Current') ?></td>
                <td class="td1" width="25%"><?= yii::t('install', 'Least Required') ?></td>
            </tr>
            <tr>
                <td><?= yii::t('install', 'OS') ?></td>
                <td>Unix-like</td>
                <td><i class="fa fa-check correct"></i> <?= $os ?></td>
                <td><?= yii::t('install', 'Unrestricted') ?></td>
            </tr>
            <tr>
                <td><?= yii::t('install', 'PHP Version') ?></td>
                <td>>5.6.x</td>
                <td><?= $phpversion ?></td>
                <td>5.4.0</td>
            </tr>
            <!-- 模块检测 -->
            <tr>
                <td class="td1" colspan="4">
                    <?= yii::t('install', 'Module') ?>
                </td>
            </tr>
            <tr>
                <td>session</td>
                <td><?= yii::t('install', 'Required') ?></td>
                <td>
                    <?= $session ?>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
            </tr>
            <tr>
                <td>
                    PDO
                    <a href="https://www.baidu.com/s?wd=开启PDO,PDO_MYSQL扩展" target="_blank">
                        <i class="fa fa-question-circle question"></i>
                    </a>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
                <td>
                    <?= $pdo ?>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
            </tr>
            <tr>
                <td>
                    PDO_MySQL
                    <a href="https://www.baidu.com/s?wd=开启PDO,PDO_MYSQL扩展" target="_blank">
                        <i class="fa fa-question-circle question"></i>
                    </a>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
                <td>
                    <?= $pdo_mysql ?>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
            </tr>
            <tr>
                <td>
                    CURL
                    <a href="https://www.baidu.com/s?wd=开启PHP CURL扩展" target="_blank">
                        <i class="fa fa-question-circle question"></i>
                    </a>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
                <td>
                    <?= $curl ?>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
            </tr>
            <tr>
                <td>
                    GD
                    <a href="https://www.baidu.com/s?wd=开启PHP GD扩展" target="_blank">
                        <i class="fa fa-question-circle question"></i>
                    </a>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
                <td>
                    <?= $gd ?>
                </td>
                <td><?= yii::t('install', 'Required') ?></td>
            </tr>
            <!-- 大小限制检测 -->
            <tr>
                <td class="td1" colspan="4">
                    <?= yii::t('install', 'Other') ?>
                </td>
            </tr>
            <tr>
                <td><?= yii::t('install', 'Upload Limit') ?></td>
                <td>>2M</td>
                <td>
                    <?= $upload_size ?>
                </td>
                <td><?= yii::t('install', 'Unrestricted') ?></td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td class="td1"><?= yii::t('install', 'Directory File Permission') ?></td>
                <td class="td1" width="25%"><?= yii::t('install', 'Writtable') ?></td>
                <td class="td1" width="25%"><?= yii::t('install', 'Readable') ?></td>
            </tr>
            <?php
            foreach ($folders as $dir => $vo) {
                ?>
                <tr>
                    <td>
                        <?= $dir ?>
                    </td>
                    <td>
                        <?php if ($vo['w']) { ?>
                            <i class="fa fa-check correct"></i> <?= yii::t('install', 'Yes') ?>
                        <?php } else { ?>
                            <i class="fa fa-remove error"></i> <?= yii::t('install', 'No') ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($vo['r']) { ?>
                            <i class="fa fa-check correct"></i> <?= yii::t('install', 'Yes') ?>
                        <?php } else { ?>
                            <i class="fa fa-remove error"></i> <?= yii::t('install', 'No') ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="bottom text-center">
        <a href="<?= Url::to(['check-environment']) ?>" class="btn btn-primary"><?= yii::t('install', 'Refresh') ?></a>
        <a href="<?= Url::to(['setinfo']) ?>" <?php if ($err > 0) {
            echo "onclick=\"alert('" . yii::t('install', 'Please check your environment to suite the cms') . "');return false;\"";
        } ?> class="btn btn-primary"><?= yii::t('install', 'Next') ?></a>
    </div>
</section>
</div>
