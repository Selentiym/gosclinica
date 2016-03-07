<?php
		function renderArrow($value, $arg)
		{
			echo CHtml::tag('span',array('style' => 'display:block;width:40px;height:20px;text-align:center;background:#49afcd;cursor:pointer;border-radius:5px;','onClick' => 'TakePage('.$arg.')'),$value);
		}
		if (is_a($clinic, 'clinics'))
		{
			?>
			<div class="left_arrow" style="width:4.5%;vertical-align: middle; display:inline-block;">
				<?php
					if ($left) {
						echo "<img style='width:100%; display:inline-block;' src='".Yii::app() -> baseUrl."/images/left_lister.png' onClick='TakePage(".($page - 1).")'/>";
					}
				?>
			</div>
			<div style="width:90%;vertical-align: middle; display:inline-block;">
			<?php $this -> renderPartial('//home/_single_clinics', array('data' => $clinic)); ?>
			</div>
			<div class="right_arrow" style="width:4.5%;vertical-align: middle;display: inline-block;">
				<?php
				if ($right) {
					echo "<img style='width:100%; display:inline-block;' src='".Yii::app() -> baseUrl."/images/right_lister.png' onClick='TakePage(".($page + 1).")'/>";
				}
				?>
			</div>
			<?php
		}
?>