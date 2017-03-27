<?php $cs = Yii::app()->getClientScript(); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/objects_list.css'); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/clinicsView.css'); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/doctorsView.css'); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/rateit.css?' . time()); ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/map.js'); ?>
<?php $cs -> registerScriptFile("https://api-maps.yandex.ru/2.1/?lang=ru_RU"); ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.rateit.min.js?' . time()); ?>
<?php Yii::app()->getClientScript()->registerScriptFile("https://docdoc.ru/widget/js", CClientScript::POS_BEGIN); ?>
<?php $cs -> registerScript('Rate','Rate()',CClientScript::POS_READY); ?>
<?php $cs -> registerScript('Order','
	$("#sortby a").click(function(e){
		
		$("#sortByField").val($(this).attr("sort"));
		$("#searchForm").submit();
		return false;
	});
',CClientScript::POS_READY);
$cs -> registerScript('Insets','
	$("#personal_object_cont .menu  .item").click(function(){
		$("#personal_object_cont .menu  .item").each(function(){
			$("#"+$(this).attr("data-word")).css("display","none");
			$(this).removeClass("active");
		});
		$(this).addClass("active");
		$("#"+$(this).attr("data-word")).css("display","block");
	});
',CClientScript::POS_READY); 
$cs -> registerScript('show_hide','
	var cont;
	$(".show").click(function(){
		$(this).hide();
		$(this).parent().children(".full").show();
		$(this).parent().children(".short").hide();
		$(this).parent().children(".hide").show();
		var cont = $(this).parents(".single_review");
		cont.saveHeight = cont.css("max-height");
		cont.css("max-height","none");
		//$(this).parent().css("max-height","none");
		
	});
	$(".hide").click(function(){
		$(this).parent().children(".full").hide();
		$(this).parent().children(".short").show();
		$(this).hide();
		$(this).parent().children(".show").show();
	});
',CClientScript::POS_READY);
	$rez = '';
	foreach ($model -> giveCoordsArray() as $name => $coords) {
		$rez .= 'addCoords(['.$coords.'],"'.$name.', '.$adress.'");';
	}
	$cs -> registerScript('map',
		'myMap=false; '.$rez
	,CClientScript::POS_READY);
	//$this -> renderPartial('//home/searchForm', array('filterForm' => $filterForm, 'modelName' => $modelName, 'fromPage' => $fromPage,'page' => $page));
?>
<div id="personal_background" class="h-card">
<div class="content_block" id="personal_object_cont">
	<div id="links">
		<a href="<?php echo Yii::app() -> baseUrl.'/'; ?>">Главная</a>
		<?php $val = $_POST["clinicsSearchForm"]["speciality"] ? $_POST["clinicsSearchForm"]["speciality"] : $_POST["doctorsSearchForm"]["speciality"]; ?>
		<a href="<?php echo Yii::app() -> baseUrl .'/'. $modelName.'?clear=';?>"><?php echo $modelName == "clinics" ? 'Клиники' : 'Врачи' ; ?></a>
		
		
		<a href="#" class="p-name"><?php echo $model -> name; ?></a>
	</div>
	<div class="main_part">
		<div class="left_side">
			
		</div>
		<div class="center">
			<div class="image_cont">
				<img class="u-logo" src="<?php echo $model -> giveLogoUrl();?>" alt="<?php echo $model->name; ?>"/>
			</div>
			<h2 class="name object_name"><?php echo $model -> name; ?></h2>
			<div class="rateit" data-rateit-value="<?php echo $model->rating; ?>" data-rateit-ispreset="true" data-rateit-readonly="true"></div>
			<div class="specialities">
				<?php
					$spec_arr = $model -> giveSpecialities();
					asort($spec_arr);
					echo CHtml::giveStringFromArray($spec_arr, ',');
				?>
			</div>
			<?php if ($data -> experience) : ?>
			<div class="experience">
				<?php echo get_class($data) == 'clinics' ? 'Существует ' : 'Стаж ' ;  echo $data -> experience; ?> лет
			</div>
			<?php endif; ?>
			<div class="object_text">
				<?php echo $model -> text; ?>
			</div>
			
			<div class="assign_cont objects_cont">
				<!--<div class="assign"><a href="<?php echo  Yii::app() -> baseUrl;?>/assign"><span>Записаться на прием</span></a></div>-->
				<?php
				$id = "DDWidgetButton_".$model -> verbiage;
				Yii::app()->getClientScript()->registerScript("turn_on_widget_".$id,"
 DdWidget({
  widget: 'Button',
  template: 'Button_common',
  pid: '9705',
  id: '".$id."',
  container: '".$id."',
  action: 'LoadWidget',
  city: 'msk'
});
", CClientScript::POS_READY); ?>
				<div id="<?php echo $id; ?>"></div>
				<?php $price = current($pricelist); ?>
				<?php if ($price) : ?>
				<div class="consult"><div class="price_img"></div><span>Консультация специалиста <?php echo $price -> price; ?></span></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="right_side">
			<div id="map"></div>
		</div>
		<div id="left_column">
			<div id="doc_spec">
				<div class="im"><span></span></div>
				<div class="content">
					<span class="heading">Специализация:</span>
					<?php foreach ($spec_arr as $spec) {
						echo "<br/> - ".$spec;
					} ?>
				</div>
			</div>
			<?php if ($model -> working_hours) : ?>
			<div class="small_info" style="background:white">
				<div class="time" style="margin:none;">
					<div class="time_img"></div>
					<div class="text"><?php echo $model -> working_hours; ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (($addr = $model -> giveAddressString('<br/>'))||($model -> phone)): ?>
			<div id="doc_info">
				<div class="im"><span></span></div>
				<div class="content">
					<span class="heading">Информация о враче:</span>
					<?php
						if ($addr) {
							echo "<p class='p-adr'>".$addr.'</p>';
						}
						if ($model -> phone) {
							echo "<p class='p-tel'>"."Запись по телефону: ".$model -> phone.'</p>';
						}
					?>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($model -> education) : ?>
			<div id="doc_ed">
				<div class="im"><span></span></div>
				<div class="content">
					<span class="heading">Образование:</span>
					<?php
						echo $model -> education;
					?>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($model -> curses) : ?>
			<div id="doc_qual">
				<div class="im"><span></span></div>
				<div class="content">
					<span class="heading">Курсы повышения квалификации:</span>
					<?php
						echo $model -> curses;
					?>
				</div>
			</div>
			<?php endif; ?>
			<?php if (count ($pricelist) > 0) : ?>
			<div id="doc_serv">
				<div class="im"><span></span></div>
				<div class="content">
					<span class="heading">Услуги:</span>
					<?php
						foreach($pricelist as $service) {
							echo "<br/> - ".$service -> name;
						}
					?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<div id="right_column">
			<div id="reviews">
			<?php $this -> renderPartial('//home/_doctor_reviews', array('id'=>$model -> id,'reviews' => $model->comments)); ?>
			</div>
		</div>
	</div>
	

	

</div>
</div>
<?php if (!empty($similar)) : ?>
<div id="other_docs" class="content_block">
	<h3>Другие специалисты:</h3>
	<?php
		foreach($similar as $doc){
			echo "<div class='dop_doc'>";
			$this -> renderPartial('//home/_short_doctor',array('data' => $doc));
			echo "</div>";
		}
	?>
</div>
<?php endif; ?>