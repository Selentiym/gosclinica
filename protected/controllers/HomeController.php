<?php
class HomeController extends Controller
{
	public $layout='//layouts/main';
	public $defaultAction = 'articles';
	public $human_verbiages = array(
		'head',
		'arm',
		'leg',
		'stomach',
		'belt',
		'chest',
		'general'
	);
	//public $pageTitle;
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index', 'search','articles'),
				'users'=>array('*'),
			)
		);
	}
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
	public function actionAssign(){
		if (strlen($_POST['subm']) > 0) {
			if ((strlen(trim($_POST["fio"]))>0)&&(strlen(trim($_POST["tel"]))>0)) {
				$fio = $_POST["fio"];
				$tel = $_POST["tel"];
				$date=date("d.m.y"); // число.месяц.год 
				$time=date("H:i"); // часы:минуты:секунды 
				$headers = "From: gosclinica@mail.ru\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html\r\n";
				$mail = "Дата: <strong>{$date}</strong><br/>";
				$mail .= "Время: <strong>{$time}</strong><br/>";
				$mail .= "Имя: <strong>{$fio}</strong><br/>";
				$mail .= "Телефон: <strong>{$tel}</strong><br/>";
				mail("podorozhkin_d@mail.ru", "Заявка на звонок от пользователя {$fio}", $mail, $headers); //*/
				CHtml::redirectAfterInform('Заявка отправлена!', $_POST["from"]);
				//$this -> redirect($_POST["from"]);
				//echo "gfc";
			} else {
				echo "Заполните оба поля!";
			}
			//echo "podorozhkin_d@mail.ru";
		}
		$this -> renderPartial('//home/_assignForm',array(),false,true);
	}
	/*
	* Displays a list of root articles.
	*/
	public function actionArticles()
	{
		$this -> layout = 'mainLayout';
		/*$stack = $this -> human_verbiages;
		$arts = Articles::GiveArticlesById(0, true);*/
		
		$criteria = new CDbCriteria;
		$criteria -> compare('level',0);
		$criteria -> compare('parent_id',0);
		$criteria -> compare('id_type',ArticleType::model() -> getNumber('bibl'));
		$articles = Articles::model() -> findAll($criteria);
		
		$arts = array();
		foreach ($articles as $art) {
			$arr = array();
			$arr['name'] = $art -> name;
			$arr['verbiage'] = $art -> verbiage;
			$arr['id'] = $art -> id;
			$arr['text'] = $art -> text;
			
			$arts[] = $arr;
		}
		
		/*$arts = array_filter($arts,function ($art) use ($stack){
			return (!in_array($art ["verbiage"], $stack));
		});*/
		$this->render('//articles/show_all', array(
			//'articles' => Articles::GiveArticlesById(0, true)
			'articles' => $arts
		));
	}
	/**
	 * Displays the main page.
	 */
	/*public function actionIndex()
	{
		$criteria = new CDbCriteria;
		$criteria -> order = 'position ASC';
		$setting = Setting::model() -> find();
		
		$this -> renderPartial('index_new',array(), false, true);
		
		/*$this -> renderPartial('index', array(
			'articles' => Articles::GiveArticlesById(0, true),
			'RightTexts' => RightText::model() -> findAll($criteria),
			'HorizontalTexts' => HorizontalText::model() -> findAll($criteria),
			'main_text' => $setting -> main_text,
			'footerMenu' => MenuButtons::model() -> PrepareFooterMenu(),
			'footerText' => $setting -> footer_text
		),false, true);
	}//*/
	/**
	 * Displays th search result
	 */
	public function actionSearch()
	{
		$this -> render('search',array(
			
		));
	}
	/**
	 * creates a test model of the article and views it to the user.
	 */
	public function actionArticlePreview(){
		$model = new Articles;
        $menuLevel = 0;
        
        if(isset($_POST['Articles']))
        {   
            $model->attributes = $_POST['Articles'];
			if ((!isset($_POST["Articles"]["parent_id"]))&&($_POST["level"]==0))
			{
				$model -> parent_id = 0;
			}
			if (isset($_POST["triggers_array"]))
			{
				$model -> trigger_value_id = implode(';',$_POST["triggers_array"]);
			} else {
				$model -> trigger_value_id = 0;
			}
        }
		$this -> layout = 'mainLayout';
		$this->render('//articles/view', array(
			'model' => $model,
			'children' => array(),
			'parentList' => $model -> GiveParentList($model),
			//'clinic' => $clinic,
			//'left' => $left,
			//'right' => $right,
			//'page' => $page
		));
	}
	/**
	 * Displays a particular.
	 */
	public function actionviewArticle($verbiage, $hash = null)
	{
		$verbiage = explode("/",$verbiage);
		$article_array = Articles::model() -> giveArticleContent(trim(end($verbiage)));
		
		//meta tags
		Yii::app()->clientScript->registerMetaTag($article_array['article']->keywords, 'keywords');
		Yii::app()->clientScript->registerMetaTag($article_array['article']->description, 'description');
		//Набор клиник для показа под статьей.
		//$this->clinics = clinics::model()->findAll(array('with' => 'services', 'order' => 'rating DESC'));
		//print_r($article_array['article']['trigger_value_id']);
		//Если у статьи выключено отображение клиник под ней, то не пытаемся этого делать.
		$fl1 = $article_array['article'] -> show_objects;
		$fl2 = (Setting::model()->find()->show_objects);
		//echo 'art:'.$fl1;
		//echo 'set:'.$fl2;
		//echo 'trigs:'.$article_array['article']['trigger_value_id'];
		/*$clinic = new clinics();
		$clinic -> filterByTriggerValuesIdArray();*/
		if (($article_array['article'] -> show_objects)&&(Setting::model()->find()->show_objects)) {
			//save the trigger ids
			$search = array_filter(array_map('trim',explode(';',$article_array['article']['trigger_value_id'])));
			//save the metro
			if ($article_array['article'] -> metro_station) {
				$search ['metro'] = array_filter(array_map('trim',explode(';', $article_array['article'] -> metro_station)));
			}
			if ($article_array['article'] -> clinicName) {
				$search['name'] = $article_array['article'] -> clinicName;
			}
			if (!$search) {$search = array();}
			$clinics = clinics::model() -> userSearch($search, 'rating');
			$clinics = $clinics['objects'];
			//$clinics = clinics::model() -> filterByTriggerValuesIdString(clinics::model() -> findAll(array('order' => 'rating DESC')), $article_array['article']['trigger_value_id']);
		} else {
			$clinics = array();
		}
		//echo count($clinics);
		if (!isset($_GET['page']))
		{
			$page = 0;
		} else {
			$page = ($_GET['page'] + 1 > count($clinics)) ? 0 : $_GET['page'];
		}
		$left = ($page > 0);
		$right = ($page + 1 < count($clinics));
		if (!empty($clinics))
		{
			$clinic = $clinics[$page];
			$clinic -> ReadData();
		} else {
			$clinic = '';
		}
		$this -> layout = 'mainLayout';
		$this->render('//articles/view', array(
			'model' => $article_array['article'],
			'children' => $article_array['children'],
			'parentList' => $article_array['parents'],
			'clinic' => $clinic,
			'left' => $left,
			'right' => $right,
			'page' => $page
		));
	}
	/**
	 * Displays a set of models with the filters
	 */
	public function actionViewModelList($modelName)
	{
		$pageSize = BaseModel::PAGE_SIZE;
		if ($_POST["sortBy"]) {
			$order = $_POST["sortBy"];
		}
		//echo $order;
		/*if (!$modelName) {
			
		}*/
		$sess_data = Yii::app()->session->get($modelName.'search');
		$searchId = $_GET["search_id"];
		$fromSearchId = TriggerValues::model() -> decodeSearchId($searchId);
		//print_r($fromSearchId);
		//echo "<br/>";
		//Если задан $_POST/GET с формы, то сливаем его с массивом из searchId с приоритетом у searchId
		if ($_POST[$modelName.'SearchForm'])
		{
			$fromPage = array_merge($_POST[$modelName.'SearchForm'], $fromSearchId);
		} else {
			//Если же он не задан, то все данные берем из searchId
			$fromPage = $fromSearchId;
		}
		//print_r($fromPage);
		if ((!$fromPage)&&(!$sess_data))
		{
			//Если никаких критереев не задано, то выдаем все модели.
			$searched = $modelName::model() -> userSearch(array(),$order);
		} else {
			if ($_GET["clear"]==1)
			{
				//Если критерии заданы, но мы хотим их сбросить, то снова выдаем все и обнуляем нужную сессию
				Yii::app()->session->remove($modelName.'search');
				$page = 1;
				//was://$searched = $modelName::model() -> userSearch(array(),$order);
				$searched = $modelName::model() -> userSearch($fromSearchId,$order);
			} else {
				//Если же заданы какие-то критерии, но не со страницы, то вместо них подаем данные из сессии
				if (!$fromPage)
				{
					$fromPage = $sess_data;
					//echo "from session";
				}
				//Адаптируем критерии под специализацию. Если для данной специализации нет какого-то критерия, а он где-то сохранен, то убираем его.
				$fromPage = Filters::model() -> FilterSearchCriteria($fromPage, $modelName);
				//Если критерии заданы и обнулять их не нужно, то запускаем поиск и сохраняем его критерии в сессию.
				Yii::app()->session->add($modelName.'search',$fromPage);
				$searched = $modelName::model() -> userSearch($fromPage,$order);
			}
		}
		//делаем из массива объектов dataProvider
        $dataProvider = new CArrayDataProvider($searched['objects'],
            array(  'keyField' =>'id'
                ));
		$this -> layout = 'layoutNoForm';
		//Определяем страницу.
		$maxPage = ceil(count($searched['objects'])/$pageSize);
		if ($_GET["page"]) {
			$_POST["page"] = $_GET["page"];
		}
		$page = $_POST["page"] ? $_POST["page"] : 1;
		$page = (($page >= 1)&&($page <= $maxPage)) ? $page : 1;
		$_POST[$modelName.'SearchForm'] = $fromPage;
		$this->render('show_list', array(
			'objects' => array_slice($searched['objects'],($page - 1) * $pageSize, $pageSize),
			'modelName' => $modelName,
			'filterForm' => $modelName::model() -> giveFilterForm($fromPage),
			'fromPage' => $fromPage,
			'description' => $searched['description'],
			'specialities' => Filters::model() -> giveSpecialities($modelName),
			'page' => $page,
			'maxPage' => $maxPage,
			'total' => count($searched['objects'])
		));
		
	}
	/**
	 * Функция для обработки ajax запроса о смене страницы от отображалки карточки клиники
	 *  под статьей.
	 */
	public function actionListPage()
	{
		if(!Yii::app()->request->isAjaxRequest) throw new CHttpException('Url should be requested via ajax only');
		if (isset($_GET["verbiage"]))
		{
			$verbiage = $_GET["verbiage"];
			$article_array = Articles::model() -> giveArticleContent(trim($verbiage));
			//save the trigger ids
			$search = array_filter(array_map('trim',explode(';',$article_array['article']['trigger_value_id'])));
			//save the metro
			if ($article_array['article'] -> metro_station) {
				$search ['metro'] = array_filter(array_map('trim',explode(';', $article_array['article'] -> metro_station)));
			}
			if (!$search) {$search = array();}
			$clinics = clinics::model() -> userSearch($search, 'rating');
			$clinics = $clinics['objects'];
			//$clinics = clinics::model() -> filterByTriggerValuesIdString(clinics::model() -> findAll(array('order' => 'rating DESC')), $article_array['article']['trigger_value_id']);
			if (!isset($_GET['page']))
			{
				$page = 0;
			} else {
				$page = ($_GET['page'] + 1 > count($clinics)) ? 0 : $_GET['page'];
			}
			$left = ($page > 0);
			$right = ($page + 1 < count($clinics));
			$clinic = $clinics[$page];
			$clinic -> ReadData();
			$this->renderPartial('//home/viewLister', array(
				'clinic' => $clinic,
				'left' => $left,
				'right' => $right,
				'page' => $page
			));
		}
	}
	public function actionSetVerbiage(){
		$values = TriggerValues::model() -> findAll();
		foreach ($values as $value)
		{
			if (strlen($value -> verbiage) == 0){
				echo str2url($value -> value)."<br/>";
				$value -> verbiage = str2url($value -> value);
				$value -> save();
			} else {
				echo "verbiage: ".str2url($value -> verbiage)."<br/>";
			}
		}
	}
	
	public function actionViewModel($modelName, $verbiage)
	{
		$word = $_GET["word"] ? $_GET["word"] : 'main' ;
		$this -> ViewSingleModel($modelName, $verbiage, $word);
	}
	/*public function actionViewModelOther($modelName, $verbiage)
	{
		$this -> ViewSingleModel($modelName, $verbiage, true);
	}*/
	/**
	 * Displays a model with a specified verbiage
	 */
	public function ViewSingleModel($modelName, $verbiage, $word)
	//public function actionViewModel($modelName, $verbiage)
	{
		//echo "model:";
		//echo $modelName.'<br/>';
		//echo $verbiage.'<br/>';
		//return;      
		if ($object = $modelName::model() -> find('verbiage=:verb', array(':verb' => $verbiage)))
		{
			$criteria = new CDbCriteria();
			//var_dump($object);
			$criteria -> compare('object_id', $object -> id);
			$criteria -> compare('object_type', $object -> type);
			$pricelist = PriceList::model()->findAll($criteria);
			//$pricelist = PriceList::model()->findAll(array('condition' => array ('object_id = ' . $object->id, 'object_type = '. $object -> type)));

			// meta tags
			Yii::app()->clientScript->registerMetaTag($object->keywords, 'keywords');
			Yii::app()->clientScript->registerMetaTag($object->description, 'description');
			$this -> pageTitle = $object -> title;
			$this -> layout = 'mainLayout';
			//if (!strpos($_SERVER['REQUEST_URI'], '/other')) {
			//if (!$other) {
			$sess_data = Yii::app()->session->get($modelName.'search');
			if (empty($sess_data)) {
				$fromPage['speciality'] = key($object -> giveAllSpecialities());
				$fromPage['district']='';
				$fromPage['metro']=0;
			} else {
				$fromPage = $sess_data;
			}
			$similar = $object -> userSearch($fromPage,'rating', 4);
			
			$this->render($modelName.'View',array(
				'model' => $object,
				'pricelist' => $pricelist,
				'word' => $word,
				'similar' => $similar['objects'],
				'modelName' => $modelName
			));
			/*} else {
				//$this->layout='main';
				$this->render($modelName.'Other',array(
					'model' => $object,
					'add_comment' => new Comments(),
					'isNew' => true
				));
			}//*/
		} else {
			$this -> render('//site/error', array(
				'code' => '404',
				'message' => 'Не найден объект типа '.$modelName.' с адресом '. $verbiage.'.'
			));
		}
		//echo "in construct";
	}
	public function actionGenerateSId()
	{
		if(Yii::app()->request->isAjaxRequest) {
			echo TriggerValues::model() -> codeSearchId($_GET['data']);
		} else {
			echo "This page is only for ajax requests.";
		}
	}
	public function actionCheck()
	{
		$this -> render('//searchid/triggerForm', array(
			'id' => 'triggers'
		));
	}
	/**
	 * Saves an added comment.
	 */
	public function actionComment($modelName)
	{
		$model = new Comments;
		$object = $modelName::model() -> findByPk($_POST["object_id"]);
		
		$isNew = true;
		
		if(isset($_POST['Comments']))
		{
			
			$model->attributes=$_POST['Comments'];
			$model->user_first_name = trim($_POST['Comments']['user_first_name']);
			$model->object_id = $_POST['object_id'];
			$model->object_type = Objects::model() -> getNumber($modelName);
			
			//print_r($_POST);
			
			if($model->save()) {
				Yii::app()->user->setFlash('commentSuccessfull', CHtml::encode('Спасибо, Ваш комментарий будет добавлен после проверки администратором.'));
				//$this->redirect($this->createUrl('/'.$modelName.'/'. $_POST['verbiage'] .'/other'));
				$this -> redirect(Yii::app() -> baseUrl.'/'.$modelName.'/reviews/'.$object -> verbiage);
				$isNew = false;
			} else {
				$errors = $model->getErrors();
				$error_message = '';
				//$error_message = '<ul>';
				foreach ($errors as $error) {
					$error_message .= implode('<br/>', $error);
					//$error_message .= '<li>' . implode('<br/>', $error) . '</li>';
				}
				//$error_message .= '</ul>';

				Yii::app()->user->setFlash('commentFailed', CHtml::encode('Не удалось добавить комментарий') . '<br/>'.$error_message."<br/>");
			} 
			
			$this -> redirect(Yii::app() -> baseUrl.'/'.$modelName.'/reviews/'.$object -> verbiage);
			/*$this->layout='main';
			$this->render($modelName.'Other', array('model' => $object, 'add_comment' => $model, 'isNew' => $isNew));*/
		}
	}
	
	public function actionHuman(){
		$criteria = new CDbCriteria;
		$criteria -> addInCondition('verbiage',$this -> human_verbiages);
		$criteria -> compare('level', 0);
		$roots = Articles::model() -> findAll($criteria);
		$this -> layout = 'mainLayout';
		$this -> render('//home/human', array(
			'roots' => $roots
		));
	}

	/**
	 * Делаем вместо двух триггеров со специализациями один с двумя названиями.
	 */
	/*public function actionRemakeTriggers(){
		$criteria = new CDbCriteria();
		$criteria -> compare('trigger_id', 26);
		//Нашли все значения триггеров, которые нужно перенести.
		$clinVals = TriggerValues::model() -> findAll($criteria);
		//Пробегаем по ним и пытаемся найти двойственный триггер
		$countOk = 0;
		$count = 0;
		//Will contain pairs 26-value => 25-value
		$map = array();
		foreach($clinVals as $val){
			$criteria = new CDbCriteria();
			$criteria -> compare('trigger_id', 25);
			$criteria -> compare('value',mb_substr($val -> value,0,-3,'UTF-8'),true);
			$trig = TriggerValues::model() -> find($criteria);

			if ($trig) {
				$map[$val -> id] = $trig -> id;
				$countOk ++;
				echo $val -> value.' - '.$trig -> value.'<br/>';
			} else {
				$map[$val->id] = 'nothing';
				echo $val->value . ' not found<br/>';
			}
			$count ++;
		}
		echo "found {$countOk}, all {$count}";
		var_dump($map);
		echo "<pre>";
		echo json_encode($map,JSON_PRETTY_PRINT);
		echo "</pre>";
	}*/
	public function actionRemakeTriggers(){
		$str = file_get_contents(Yii::getPathOfAlias('webroot').'/26to25.json');
		$arr = json_decode($str, true);
		//var_dump($arr);
		//Меняем названия триггеров на нужные.
		/*foreach($arr as $id26 => $id25) {
			$trig25 = TriggerValues::model() -> findByPk($id25);
			$trig26 = TriggerValues::model() -> findByPk($id26);
			$trig25 -> value = $trig26 -> value;
			$trig25 -> save();
		}*/
		//Получили таблицу соответствия, теперь пробегаем по всем клиникам и меняем триггеры.
		$clinics = clinics::model() -> findAll();
		foreach($clinics as $clinic) {
			$was = TriggerValues::model() -> findAllByPk(explode(';',$clinic -> triggers));
			$newtr = str_replace(array_keys($arr),array_values($arr), $clinic -> triggers);
			echo $clinic -> triggers. ' - ' .$newtr.'<br/>';
			$is = $was = TriggerValues::model() -> findAllByPk(explode(';',$newtr));
			echo $clinic -> verbiage.'<br/>';
			echo "was: ".implode(' ', Html::listData($was,'id','value')).'<br/>';
			echo "is: ".implode(' ', Html::listData($is,'id','value')).'<br/>';
			$clinic -> triggers = $newtr;
			$clinic -> save();
		}
	}
}
?>