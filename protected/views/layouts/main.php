﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    
    <!-- custom CSS -->
    <?php Yii::app()->bootstrap->register(); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/styles.css'); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/custom.css'); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/clinics.css'); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/jquery-ui.css'); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/rateit.css?' . time()); ?>
    <?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/jqcloud.css'); ?>

    <?php Yii::app()->getClientScript()->registerCoreScript('jquery'); ?>
    <?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.rateit.min.js?' . time()); ?>
    <?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/jqcloud-1.0.4.min.js'); ?>
    <?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/functions.js'); ?>
	<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/scroller.js'); ?>
	<?php Yii::app()->clientScript->registerScript('MakeStars','Rate();', CClientScript::POS_END );?>
    <script type="text/javascript" src="//vk.com/js/api/openapi.js?116"></script>
	<?php
	$title = $this -> getPageTitle();
	$title = $title ? $title : Yii::app() -> name;
	?>
    <title><?php echo $title; ?></title>
</head>

<body>

    <?php Yii::app()->controller->renderPartial('//layouts/_top_banner'); ?>

    <?php Yii::app()->controller->renderPartial('/home/_top_menu'); ?>
	<div class="scrollup">
		<a href="#" >Наверх</a>
	</div>
    <div id="container" class="container-fluid">
        <div class="row-fluid">
            <?php Yii::app()->controller->renderPartial('/home/_leftside_menu'); ?>
            <?php echo $content; ?>
            <?php Yii::app()->controller->renderPartial('//layouts/_side_banner'); ?>
        </div>
        <?php Yii::app()->controller->renderPartial('//layouts/_footer'); ?>

    </div> <!-- container -->
    
</body>
</html>
