<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/human.css'); ?>

<?php
	Yii::app()->getClientScript() -> registerScript('pointHover','
		function BlockChosen(keyAttr,cont){
			var selector = " div[data-keyattr=\'"+keyAttr+"\']";
			$("#rezults").children().hide();
			$("#rezults" + selector).show();
			$("#sideMenu div.item").removeClass("active");
			$("#sideMenu" + selector).addClass("active");
			if (!cont) {
				cont = $("#background_container"+selector);
			}
			$(".mask").hide();
			cont.children(".mask").show();
		}
		$(".point").mouseover(function(){
			var cont = $(this).parent();
			var keyAttr = cont.attr("data-keyattr");
			BlockChosen(keyAttr, cont);
		});
		/*$(".point").mouseout(function(){
			var cont = $(this).parent();
			cont.children(".mask").hide();
		});*/
		$("#sideMenu div.item").mouseover(function(){
			BlockChosen($(this).attr("data-keyattr"),false);
		});
	',CClientScript::POS_END);
?>
<div class="content_block">
	<div id="jaloba_center"><span>Жалоба/Симптом</span></div>
	<div class="center">Симптомы болезней, внешние признаки заболеваний</div>
	<div id="picture_block">
		<div id="main_picture">
			<div id="inline_cont">
				<div id="background_container">
					<div data-keyattr="head" id="head"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="leg" id="right_leg"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="leg" id="left_leg"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="stomach" id="stomach"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="chest" id="chest"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="arm" id="right_arm"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="arm" id="left_arm"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="belt" id="belt"><div class="point"></div><div class="mask" style="display:none"></div></div>
					<div data-keyattr="general" id="general"><div class="point"></div><div class="mask" style="display:none"></div></div>
				</div>
			</div>
		</div>
	</div>
	<div id="text_block">
		<div id="sideMenu">
			<?php
				$act = true;
				foreach ($roots as $root) {
					echo "<div class='item".($act ? ' active' : '')."' data-keyattr='".$root -> verbiage."'>";
					echo ucfirst($root -> name);
					echo "</div>";
					$act = false;
				}
			?>
		</div>
		<div id="rezults">
			<?php
				$vis = true;
				foreach ($roots as $root) {
					if (!$vis) {
						$display = "none";
					} else {
						$display = "block";
					}
					echo "<div class='articles' data-keyattr='".$root -> verbiage."' style='display:".$display."'>";
					$med = ceil(count($root -> giveChildren())/2);
					//echo count($root -> giveChildren());
					$count = 1;
					?>
					<div class="top">Уточните, что именно Вас беспокоит</div>
					<?php
					echo "<div class='column'>";
					foreach($root -> giveChildren() as $child) {
						echo "<div>".CHtml::link($child -> name, Yii::app() -> baseUrl.'/article/'.$root -> verbiage.'/'.$child -> GenerateUrl(),array())."</div>";
						if ($count == $med) {
							
							
							echo "</div>";
							echo "<div class='column'>";
						}
						$count++;
					}
					echo "</div>";
					
					echo "</div>";
					$vis = false;
				}
			?>
		</div>
	</div>
</div>