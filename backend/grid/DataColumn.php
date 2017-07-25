<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 18:46
 */

namespace backend\grid;

/**
 * @inheritdoc
 */
class DataColumn extends \yii\grid\DataColumn
{

    public $headerOptions = [];

    public $width = '60px';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (! isset($this->headerOptions['width'])) {
            $this->headerOptions['width'] = $this->width;
        }
        $this->contentOptions = ['style' => 'word-wrap: break-word; word-break: break-all;'];
    }

}