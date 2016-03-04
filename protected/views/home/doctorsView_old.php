<?php $this->setPageTitle($model->title); ?>

<div class="row-fluid">
        <div class="span4">
					<div class="navBar">
						<!--<a href="/search">Поиск</a>-->
						<?php 
							echo CHtml::link('Поиск', '/doctors');
							echo CHtml::encode("->");
							echo CHtml::link($model -> name, '/doctors/' . $model -> verbiage);
						?>
					</div>
                    <div class="page-header">
                        <h1> <?php echo CHtml::encode($model->name); ?> </h1>
                    </div>
                        <?php if ($model->address): ?> <p> <?php echo '<b>' .CHtml::encode('Адрес: ') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->address); ?> </p> <?php endif; ?>
                        <?php if ($model->phone): ?> <p> <?php echo '<b>' .CHtml::encode('Телефон:') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->phone); ?> </p> <?php endif; ?>
                        <?php if ($model->fax): ?> <p> <?php echo '<b>' .CHtml::encode('Факс: ') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->fax); ?> </p> <?php endif; ?>
                        <?php if ($model->districts_display): ?> <p> <?php echo '<b>' .CHtml::encode('Район: ') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->districts_display); ?> </p> <?php endif; ?>
                        <?php if ($model->metros_display): ?> <p> <?php echo '<b>' .CHtml::encode('Метро: ') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->metros_display); ?> </p> <?php endif; ?>
                    
                    <?php if ($model->services): ?>
                        <p>
                            <?php echo '<b>' . CHtml::encode('Услуги: ') . '&nbsp;&nbsp;</b>'; ?>
                            <?php
                                $services = '';
                                foreach ($model->services as $service)
                                    $services .= $service->name . (!empty($service->price_from)? ' (от ' . $service->price_from . ')' :'') . ', ';
                                echo substr($services, 0, strrpos($services, ','));
                            ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($model->working_days): ?>
                        <p>
                            <?php echo '<b>' . CHtml::encode('Часы работы:') . '&nbsp;&nbsp;</b>' .  (trim($model->working_days) != ''? CHtml::encode($model->working_days): '') . (trim($model->working_hours) != ''? ', ' . CHtml::encode($model->working_hours): ''); ?>
                        </p>
                    <?php endif;?>
                    
                    <?php if ($model->site): ?>    
                        <p>
                            <?php echo '<b>' . CHtml::encode('Сайт:') . '&nbsp;&nbsp;</b>' . CHtml::encode($model->site); //CHtml::link($model->site, $model->site, array('target' => 'blank')): '' ); ?>
    
                        </p>
                    <?php endif; ?>
                        
                    <?php if ($model->fields): ?>
                        <?php foreach ($model->fields as $field) :?>
                            <p>
                                <?php echo '<b>' . CHtml::encode($field->field->title . ':') . '&nbsp;&nbsp;</b>' . CHtml::encode($field->value); ?>
                            </p>
                        <?php endforeach; ?>    
                    <?php endif; ?>
                    <br/>
            </div>
            <div class="span2">
                <div class="topped">
                    <p><?php echo  CHtml::link('<span class="icon-headphones"></span>&nbsp;' . CHtml::encode('Слушать аудио'), Yii::app()->baseUrl.'/doctors/'.$model->verbiage . '/other#audio', array('class' => 'btn btn-warning')); ?></p><br/>
                    <p><?php echo  CHtml::link('<span class="icon-facetime-video"></span>&nbsp;' . CHtml::encode('Смотреть видео') , Yii::app()->baseUrl.'/doctors/'.$model->verbiage . '/other#video', array('class' => 'btn btn-info')); ?></p><br/>
                    <p><?php echo  CHtml::link('<span class="icon-comment"></span>&nbsp;' . CHtml::encode('Читать отзывы') , Yii::app()->baseUrl.'/doctors/'.$model->verbiage . '/other#comments', array('class' => 'btn btn-success')); ?></p>
                </div>
        </div>

        <div class="span6">
                <?php
                $images = array_map('trim', explode(';', $model->pictures));
                $images_display = array();
                foreach($images as $image) {
                    if (trim($image) != "") {
                        //$image_path = Yii::app()->baseUrl. '/images/doctors/' . $model->id . '/' . trim($image);
                        $image_path = $model -> giveImageFolderRelativeUrl() . trim($image);
                        //$image_real_path = Yii::app()->basePath. '/../images/doctors/' . $model->id . '/' . trim($image);
                        $image_real_path = $model -> giveImageFolderAbsoluteUrl() . trim($image);

                        if (file_exists($image_real_path))
                            $images_display[] = array('image' => $image_path, 'label' => '', 'alt' => $image, 'htmlOptions' => array('width' => '200px', 'height' => '150px'));
                    }
                }

                if (!empty($images_display))
                    $this->widget('bootstrap.widgets.TbCarousel', array(

                        'htmlOptions' => array (
                           
                        ),
                        'items' => $images_display
                    ));

                ?>
        </div>

</div>
    <!-- End Left side pane-->
<br/><br/>

<div class="row-fluid">
        <div class="span6">
            <table class="table table-striped table-bordered">
                <tbody>
                    <?php if (!empty($pricelist)) {
                            foreach($pricelist as $key => $value) {
                                echo '<tr><td><i class="icon-star "></i>&nbsp;&nbsp;' . CHtml::encode($value->name) . '</td><td>'. $value->price  . '</td></tr>';
                            }
                        } else {
                            echo '<tr><td>' . CHtml::encode('Прайслист отсутствует') . '</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

 <?php if ($model->triggers_display): ?>
    <div class="span6 well">
        <?php foreach($model->triggers_display as $trigger) {  ?>
            <?php echo '<span>'; ?>
            <?php 
				if (trim($trigger->trigger->logo) !='') {
                    $trigger -> trigger -> showImage(trim($trigger -> trigger -> logo), 'logo');
					//echo '<img border="1" align="ABSMIDDLE" src="' . Yii::app()->baseUrl.'/images/triggers/' . $trigger->trigger->id . '/' . $trigger->trigger->logo .'" style="max-width: 7%; max-height: 7%; padding-right: 1%;">';
				}
			?>

            <?php echo CHtml::encode($trigger->value) . '</span>&nbsp;&nbsp;';  ?>
        <?php } ?>
    </div>
<?php endif; ?>
    
</div>
<br/><br/>

<?php if ($model->text && $model->text != ''): ?>
    <div class="row-fluid thumbnail">    
            <?php
              echo $model->text;
            ?>
    </div>
<?php endif; ?>
<!--
<?php if ($model->fields): ?>
    <div class="row-fluid thumbnail">
        <?php foreach ($model->fields as $field) :?>
            <?php echo '<tr><td>' . CHtml::encode($field->field->title) . '</td></tr>'; echo $field->value; ?>
        <?php endforeach; ?>    
    </div>
<?php endif; ?>
-->

<?php if (!empty($model->map_coordinates)) { ?>
    <div class="row-fluid">
        <h3> <?php echo CHtml::encode('На карте'); ?> </h3>
        <?php
            $map_cooords = array_map('trim', explode(',', $model->map_coordinates));
            $this->widget('ext.yaMap.YaMap',
                array(
                    'points' => array(
                        array(
                            'lat' => $map_cooords[0],
                            'lng' => $map_cooords[1],
                            'icon' => '',
                            'header' => $model->name,
                            'body'=> $model->address . ', ' . $model->phone,//'Юридический центр</br><small>звонок по России бесплатный</small>',
                            //'footer' => '8-800-234-07-17',
                        ),
                    ),
                    'params' => array('visible'=>true,'zoom'=>13,'width'=>'750px','height'=>'325px'),
                )
            );
        ?>
    </div>
<?php } ?>