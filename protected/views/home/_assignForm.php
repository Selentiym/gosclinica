<form name="assign" method="post">
	<input type="hidden" name="from" value="<?php echo $_POST["from"] ? $_POST["from"] : $_SERVER['HTTP_REFERER']; ?>">
	<div>
	ФИО:
		<input type="text" name="fio"/>
	</div>
	<div>
	Телефон:
		<input type="text" name="tel"/>
	</div>
	<div>
		<input type="submit" name="subm" value="Отправить"/>
	</div>
</form>