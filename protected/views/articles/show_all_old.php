<div id="root_articles" class="span8">
	<?php // echo $this -> renderPartial('//articles/_navBar', array('article' => false)); ?>
	<table>
		<?php 
			function GiveFirstLetter($article){
				$name = (string)$article['name'];
				return mb_substr($name,0,1,'utf-8');
			}
			$count=count($articles);
			echo CHtml::openTag('ul', array('class' => 'letter_block'));
			$firstLetter = GiveFirstLetter(current($articles));
			foreach($articles as $article) {
				if ($firstLetter != GiveFirstLetter($article))
				{
					echo CHtml::closeTag('ul');
					echo CHtml::openTag('ul', array('class' => 'letter_block'));
					$firstLetter = GiveFirstLetter($article);
				}
				echo $this->renderPartial('//articles/article_shortcut', array('article'=>$article, 'baseArticleUrl' => Yii::app() -> baseUrl.'/article'));
			}
			
			/*for ($i = 0; $i < $count/3; $i++)
			{
				echo "<tr>";
				for($j = 0; $j < 3; $j++)
				{
					if ($i*3 + $j < $count)
					{
						echo "<td>";
					} else {
						echo "<td class='empty'>";
					}
					echo $this->renderPartial('//articles/article_shortcut', array('article'=>$articles[$i * 3 + $j], 'baseArticleUrl' => Yii::app() -> baseUrl.'/article'));
					echo "</td>";
				}
				echo "</tr>";
				
			}*/
		?>
	</table>
</div>