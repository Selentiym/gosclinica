<!-- HIDDEN IMPORT CSV FORM-->
<div>
    <br>
    <div>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'export-clinics',
                'action'=> Yii::app()->baseUrl.'/admin/clinicsImport',
                'clientOptions'=>array(
                    'validateOnSubmit'=>true,
                ),
                'htmlOptions' => array(
                    'style' => 'display: none',
                    'enctype'=>'multipart/form-data',
                )
            )); ?>

            <div class="row-fluid">
                <div class="control-group">
                    <input type="hidden" name="MAX_FILE_SIZE" value="600000" />
                    <input type="file" name="clinics_csv" size="60">
                </div>
                <div class="control-group">
                    <?php echo CHtml::htmlButton(CHtml::encode('Загрузить'), array('class'=>'btn btn-info', 'type'=>'submit')); ?>
                </div>
            </div>

        <?php $this->endWidget(); ?>
    </div>
</div>