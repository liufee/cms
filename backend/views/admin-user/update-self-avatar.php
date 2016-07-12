<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 17:08
 */
use yii\helpers\Url;
use feehi\widgets\ActiveForm;
?>
<link rel="shortcut icon" href="favicon.ico"> <link href="css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
<link href="css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
<link href="css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="css/plugins/chosen/chosen.css" rel="stylesheet">
<link href="css/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="css/plugins/cropper/cropper.min.css" rel="stylesheet">
<link href="css/plugins/switchery/switchery.css" rel="stylesheet">
<link href="css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">
<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
<link href="css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">
<link href="css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
<link href="css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
<link href="css/animate.min.css" rel="stylesheet">
<link href="css/style.min862f.css?v=4.1.0" rel="stylesheet">
<script src="js/jquery.min.js?v=2.1.4"></script>
<script src="js/bootstrap.min.js?v=3.3.6"></script>
<script src="js/content.min.js?v=1.0.0"></script>
<script src="js/plugins/chosen/chosen.jquery.js"></script>
<script src="js/plugins/jsKnob/jquery.knob.js"></script>
<script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="js/plugins/prettyfile/bootstrap-prettyfile.js"></script>
<script src="js/plugins/nouslider/jquery.nouislider.min.js"></script>
<script src="js/plugins/switchery/switchery.js"></script>
<script src="js/plugins/ionRangeSlider/ion.rangeSlider.min.js"></script>
<script src="js/plugins/iCheck/icheck.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="js/plugins/clockpicker/clockpicker.js"></script>
<script src="js/plugins/cropper/cropper.min.js"></script>
<script src="js/demo/form-advanced-demo.min.js"></script>
<div class="ibox float-e-margins">
    <div class="ibox-title  back-change">
        <h5>图片裁剪 <small>http://fengyuanchen.github.io/cropper/</small></h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#">选项 01</a>
                </li>
                <li><a href="#">选项 02</a>
                </li>
            </ul>
            <a class="close-link">
                <i class="fa fa-times"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <p>
            一款简单的jQuery图片裁剪插件
        </p>
        <div class="row">
            <div class="col-md-6">
                <div class="image-crop">
                    <img src="img/a3.jpg" class="cropper-hidden">
                    <div class="cropper-container" style="width: 552px; height: 552px; left: 0px; top: 0px;"><img src="file:///Users/lf/Desktop/hplus/img/a3.jpg" class="cropper-hidden"><img src="file:///Users/lf/Desktop/hplus/img/a3.jpg" class="" style="width: 553px; height: 553px; margin-left: -1px; margin-top: -1px;"><div class="cropper-canvas cropper-modal cropper-move"></div><div class="cropper-dragger" style="width: 442px; height: 273px; left: 35px; top: 25px;"><span class="cropper-viewer"><img src="file:///Users/lf/Desktop/hplus/img/a3.jpg" style="width: 553px; height: 553px; margin-left: -36px; margin-top: -25px;"></span><span class="cropper-dashed dashed-h"></span><span class="cropper-dashed dashed-v"></span><span class="cropper-face" data-directive="all"></span><span class="cropper-line line-e" data-directive="e"></span><span class="cropper-line line-n" data-directive="n"></span><span class="cropper-line line-w" data-directive="w"></span><span class="cropper-line line-s" data-directive="s"></span><span class="cropper-point point-e" data-directive="e"></span><span class="cropper-point point-n" data-directive="n"></span><span class="cropper-point point-w" data-directive="w"></span><span class="cropper-point point-s" data-directive="s"></span><span class="cropper-point point-ne" data-directive="ne"></span><span class="cropper-point point-nw" data-directive="nw"></span><span class="cropper-point point-sw" data-directive="sw"></span><span class="cropper-point point-se" data-directive="se"></span></div></div></div>
            </div>
            <div class="col-md-6">
                <h4>图片预览：</h4>
                <div class="img-preview img-preview-sm"><img src="file:///Users/lf/Desktop/hplus/img/a3.jpg" style="min-width: 0px !important; min-height: 0px !important; max-width: none !important; max-height: none !important; width: 250px; height: 250px; margin-left: -16px; margin-top: -11px;"></div>
                <h4>说明：</h4>
                <p>
                    你可以选择新图片上传，然后下载裁剪后的图片
                </p>
                <div class="btn-group">
                    <label title="上传图片" for="inputImage" class="btn btn-primary">
                        <input type="file" accept="image/*" name="file" id="inputImage" class="hide"> 上传新图片
                    </label>
                    <label title="下载图片" id="download" class="btn btn-primary">下载</label>
                </div>
                <h4>其他说明：</h4>
                <p>
                    你可以使用<code>$({image}).cropper(options)</code>来配置插件
                </p>
                <div class="btn-group">
                    <button class="btn btn-white" id="zoomIn" type="button">放大</button>
                    <button class="btn btn-white" id="zoomOut" type="button">缩小</button>
                    <button class="btn btn-white" id="rotateLeft" type="button">左旋转</button>
                    <button class="btn btn-white" id="rotateRight" type="button">右旋转</button>
                    <button class="btn btn-warning" id="setDrag" type="button">裁剪</button>
                </div>
            </div>
        </div>
    </div>
</div>
