<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 18:45
 */

namespace backend\grid;

use yii;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use feehi\assets\GridViewAsset;
use yii\helpers\Json;
use yii\widgets\BaseListView;

class GridView extends \yii\grid\GridView
{
    public $dataColumnClass = DataColumn::class;
    //public $pager = ['class'=>'source\core\widgets\AdminLinkPager'];

    public $options = ['class' => 'fixed-table-header', 'style' => 'margin-right: 0px;'];
    public $tableOptions = ['class' => 'table table-hover'];
    public $layout = "{items}\n{pager}";
    public $pagerOptions = [
        'firstPageLabel' => '首页',
        'lastPageLabel' => '尾页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'options' => [
            'class' => 'pagination',
        ],
    ];

    public $filterRow;

    public function init()
    {
        parent::init();

        $this->rowOptions = function ($model, $key, $index, $grid) {
            if ($index % 2 === 0) {
                return ['class' => 'odd'];
            } else {
                return ['class' => 'even'];
            }
        };
        $this->pagerOptions = [
            'firstPageLabel' => yii::t('app', 'first'),
            'lastPageLabel' => yii::t('app', 'last'),
            'prevPageLabel' => yii::t('app', 'previous'),
            'nextPageLabel' => yii::t('app', 'next'),
            'options' => [
                'class' => 'pagination',
            ]
        ];
    }

    public function renderTableRow($model, $key, $index)
    {
        if ($this->filterRow !== null && call_user_func($this->filterRow, $model, $key, $index, $this) === false) {
            return '';
        }
        return parent::renderTableRow($model, $key, $index);
    }

    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();
        $pager = array_merge($pager, $this->pagerOptions);
        return $class::widget($pager);
    }

    public function run()
    {
        $id = $this->options['id'];
        $options = Json::htmlEncode($this->getClientOptions());
        $view = $this->getView();
        GridViewAsset::register($view);
        $view->registerJs("jQuery('#$id').yiiGridView($options);");
        BaseListView::run();
    }
}