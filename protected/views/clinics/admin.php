<?php
/* @var $this ClinicsController */
/* @var $model clinics */

$this->breadcrumbs=array(
	'clinics'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List clinics', 'url'=>array('index')),
	array('label'=>'Create clinics', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#clinics-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage clinics</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'clinics-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'phone',
		'fax',
		'address',
		'district',
		/*
		'metro_station',
		'working_days',
		'working_hours',
		'services',
		'rating',
		'logo',
		'triggers',
		'pictures',
		'map_coordinates',
		'price',
		'text',
		'audio',
		'video',
		'comments',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>