<li class='article_shortcut'>
	<a class="a_shortcut" rel = 'nofollow' href="<?php echo $baseArticleUrl."/".$article['verbiage']; ?>">
		<?php echo $article['name'];?><!--<span class="count"><?php if ($article['c'] > 0) echo $article['c']; ?></span>-->
	</a>
	<div>
		<?php //echo CHtml::cutText(strip_tags($article['text'], 100));?>
		<?php //echo CHtml::giveFirstP($article['text']) ; ?>
	</div>
</li>