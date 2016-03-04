<?php

if (!$_GET["doctors_page"]) {
	$this -> redirect(Yii::app() -> baseUrl . '/admin/doctors?doctors_page=10000');
}
Yii::app()->clientScript->registerScript('search', "
    $('.search-button').click(function(){
        $('.search-form').toggle();
        return false;
    });
    $('.search-form form').submit(function(){
        $('#doctors-grid').yiiGridView('update', {
            data: $(this).serialize()
        });
        return false;
    });
");
Yii::app() -> clientScript -> registerScript ('clickScript','
	$("#pageButt").click(function(){
		var page = parseInt($("#pageNum").val());
		location.href="'.Yii::app() -> baseUrl.'/admin/doctors?doctors_page="+page;
	});
	$("#lastPage").click(function(){
		location.href="'.Yii::app() -> baseUrl.'/admin/doctors?doctors_page=10000";
	});
	$("#pageNum").keydown(function (e) {
    if (e.which == 13) {//13 - это код клавиши "Enter"
		var page = parseInt($("#pageNum").val());
        location.href="'.Yii::app() -> baseUrl.'/admin/doctors?doctors_page="+page;
    }
});

',CClientScript::POS_END);
?>
<?php if(Yii::app()->user->hasFlash('nothingToUpload')): ?>
    <div class="alert-danger">
        <?php echo Yii::app()->user->getFlash('nothingToUpload'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('doctorExists')): ?>
    <div class="alert-warning">
        <?php echo Yii::app()->user->getFlash('doctorExists'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('errorsWhileImporting')): ?>
    <div class="alert-danger">
        <?php echo Yii::app()->user->getFlash('errorsWhileImporting'); ?>
    </div>
<?php endif; ?>


<?php if(Yii::app()->user->hasFlash('errorUpload')): ?>
    <div class="alert-danger">
        <?php echo Yii::app()->user->getFlash('errorUpload'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('successfullUpload')): ?>
    <div class="alert-success">
        <?php echo Yii::app()->user->getFlash('successfullUpload'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('errorTruncate')): ?>
    <div class="alert-danger">
        <?php echo Yii::app()->user->getFlash('errorTruncate'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('successfulldoctorsImport')): ?>
    <div class="alert-success">
        <?php echo Yii::app()->user->getFlash('successfulldoctorsImport'); ?>
    </div>
<?php endif; ?>

<h1><?php echo CHtml::encode('Перечень докторов'); ?></h1>

<p class="pull-right">
    <?php echo CHtml::link('Добавить нового' , Yii::app()->baseUrl.'/admin/doctorsCreate', array('class' => 'btn')); ?>
</p>
<button id="lastPage">На последнюю</button>
<button id="pageButt">На страницу номер:</button><input type="text" id="pageNum"/>
<?php 
echo $_GET["page"];
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'doctors-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'enablePagination' => true,
    'summaryText' => '',
    'template'=>'{pager}{items}',
    'pager' => array(
		'class' => 'CLinkPager',
        'firstPageLabel'=>'<<',
        'prevPageLabel'=>'<',
        'nextPageLabel'=>'>',
        'lastPageLabel'=>'>>',
        'maxButtonCount'=>'10',
        'header'=>'<span>Перейти на страницу:</span>',
    ),
	
    'columns'=>array(
        'id',
        array('name' => 'name', 'header' => $model->getAttributeLabel('name')),
        array('name' => 'phone', 'header' => $model->getAttributeLabel('phone')),
        array('name' => 'site', 'header' => $model->getAttributeLabel('site')),

        array(
            'class'=>'CLinkColumn',
            'header'=>CHtml::encode('Цены'),
            'labelExpression'=>'CHtml::button("Редактировать",array("onclick"=>"document.location.href=\'".Yii::app()->createUrl("admin/doctorsPricelists", array("id"=>$data->id))."\'"))',
            'urlExpression'=>'Yii::app()->createUrl("admin/doctorsPricelists", array("id"=>$data->id))',
        ),
        /*array(
            'class'=>'CLinkColumn',
            'header'=>CHtml::encode('Услуги'),
            'labelExpression'=>'CHtml::button("Редактировать",array("onclick"=>"document.location.href=\'".Yii::app()->createUrl("admin/doctorsServices", array("id"=>$data->id))."\'"))',
            'urlExpression'=>'Yii::app()->createUrl("admin/doctorsServices", array("id"=>$data->id))',

        ),*/

        array(
            'class'=>'CLinkColumn',
            'header'=>CHtml::encode('Поля'),
            'labelExpression'=>'CHtml::button("Редактировать",array("onclick"=>"document.location.href=\'".Yii::app()->createUrl("admin/doctorsFields", array("id"=>$data->id))."\'"))',
            'urlExpression'=>'Yii::app()->createUrl("admin/doctorsFields", array("id"=>$data->id))',

        ),

      array(
            'class'=>'CButtonColumn',
            'template'=>'{update}&nbsp;{delete}',
            'deleteConfirmation'=>"js:'Вы действительно хотите удалить клинику <'+$(this).parent().parent().children(':nth-child(2)').text()+'>?'",
            'buttons'=>array
            (
                'update' => array
                (
                    'label'=> CHtml::encode('Редактировать'),
                    'url'=>'Yii::app()->createUrl("admin/doctorUpdate", array("id"=>$data->id))',
                ),
                'delete' => array
                (
                    'label'=> CHtml::encode('Удалить'),
                    'url'=>'Yii::app()->createUrl("admin/doctorsDelete", array("id"=>$data->id))',
                ),
            ),
        ),
    ),
)); ?>

<div class="row-fluid">
    <div class="span5">
        <?php echo CHtml::link('Импорт докторов', '', array('class' => 'btn btn-success', 'onclick'=> 'javascript:showImportDoctorsForm()')); ?>

    <div>
        <?php $this->renderPartial('_import_doctors'); ?>
    </div>
    </div>
    

    
    <div class="span5">
        <?php echo CHtml::link('Экспорт докторов', Yii::app()->controller->createUrl('admin/doctorsExportCsv'), array('class' => 'btn btn-warning')); ?>
    <div>
        <?php $this->renderPartial('_export_doctors'); ?>
    </div>
    </div>
    
</div>    