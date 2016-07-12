<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/7/8
 * Time: 下午2:23
 */
namespace feehi\widgets;

use yii\web\View ;
use yii\widgets\Block ;


class JsBlock extends Block{

    /**
     * @var null
     */
    public $key = null;
    /**
     * @var int
     */
    public $pos = View::POS_END ;
    /**
     * Ends recording a block.
     * This method stops output buffering and saves the rendering result as a named block in the view.
     */
    public function run()
    {
        $block = ob_get_clean();
        if ($this->renderInPlace) {
            throw new \Exception("not implemented yet ! ");
            // echo $block;
        }
        $block = trim($block) ;
        /*
        $jsBlockPattern  = '|^<script[^>]*>(.+?)</script>$|is';
        if(preg_match($jsBlockPattern,$block)){
            $block =  preg_replace ( $jsBlockPattern , '${1}'  , $block );
        }
        */
        $jsBlockPattern  = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
        if(preg_match($jsBlockPattern,$block,$matches)){
            $block =  $matches['block_content'];
        }

        $this->view->registerJs($block, $this->pos,$this->key) ;
    }
}