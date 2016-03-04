<?php

/**
 * This is the model class for table "{{articles}}" - it describes a standard Article
 *
 * The followings are the available columns in table '{{articles}}':
 * @property integer $id
 * @property string $name
 * @property string $verbiage
 * @property integer $parent_id
 * @property integer $level
 * @property string $text
 */
 
class Articles extends CTModel
{
	/**
	 * @property array children - an array of Article objects that are children to this one.
	 */
	public $children;
	protected $ParentList;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{articles}}';
	}
	public function beforeDelete() {
		if (parent::beforeDelete()) {
			$criteria = new CDbCriteria;
			$criteria -> compare('parent_id', $this -> id);
			if (self::model () -> find($criteria)) {
				new CustomFlash('error','Aricle','Delete','HasDecendants','Нельзя удалить статью, которая имеет потомков. Сначала удалите все дочерние статьи.',true);
				echo 'Нельзя удалить статью, которая имеет потомков. Сначал удалите все дочерние статьи.';
				return false;
			}
			return true;
		}
		
		
	}
    public function afterDelete()
	{
		parent::afterDelete();
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, verbiage, parent_id, level, text, show_objects', 'required'),
            array('verbiage',
                'match', 'not' => true, 'pattern' => '/[^a-zA-Z0-9_-]/',
                'message' => CHtml::encode('Запрещенные символы в поле <{attribute}>'),
            ),
			array('parent_id, level, show_objects', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('verbiage, clinic_card', 'length', 'max'=>20),
            array('title', 'length', 'max'=>255),
            array('keywords, description', 'length', 'max'=>2000),
			array('id, name, verbiage, parent_id, level, text, clinic_card, title, keywords, description, show_objects, id_type', 'safe', 'on'=>'search'),
			array('id_type','safe')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'category_' => array(self::BELONGS_TO, 'Menus',  'category', 'together' => true)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'show_objects' => 'Показывать ли под статьей карточки клиник',
			'name' => CHtml::encode('Название'),
			'verbiage' => CHtml::encode('Человекопонятный URL'),
			'category' => CHtml::encode('Категория (пункт бокового меню)'),
            'menu_sublevel' => CHtml::encode('Уровень подменю (боковое)'),
			'text' => CHtml::encode('Текст'),
            'clinic_card' => CHtml::encode('Визитка клиники'),
            'title' => CHtml::encode('Title'),
			'keywords' => CHtml::encode('Keywords'),
			'description' => CHtml::encode('Description'),
			'parent_id' => CHtml::encode('Статья-родитель'), //ссылка на статью, в которую вложена данная
			'level' => CHtml::encode('Уровень статьи'), //уровень в иерархической структуре статей 0 - раздел медицины.
			'trigger_value_id' => CHtml::encode('Значение триггеров для поиска подходящих клиник'),
			'id_type' => CHtml::encode('Тип статьи')
		);
	}
	public function getParentList()
	{
		if (!isset($this -> ParentList))
		{
			$this -> ParentList = $this -> GiveParentList($this);
		}
		return $this -> ParentList;
	}
    public function beforeSave()
    {   
        $criteria=new CDbCriteria;
        
        // prevent app from saving an article with existing URL
        if (!$this->isNewRecord) {
            $criteria->condition='id <> :id';
            $criteria->params = array(':id' => $this->id);             
        }
        
        // check for duplicates by URL
        $dups = self::model()->findByAttributes(array('verbiage' => $this->verbiage), $criteria);        
        
        if ($dups) {
            Yii::app()->user->setFlash('duplicateArticle', CHtml::encode('Статья с таким URL уже существует'));
            return false;                
        }              

        return parent::beforeSave();
    }
    
    public function giveTemporaryCriteria(){
		$criteria = new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('verbiage',$this->verbiage,true);
		//$criteria->compare('parent_id',$this->parent_id, true);
		$criteria->compare('text',$this->text,true);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('clinic_card',$this->clinic_card,true);
        $criteria->compare('keywords',$this->keywords,true);
        $criteria->compare('description',$this->description,true);
		return $criteria;
	}
	// this is standard function for searching
	public function search()
	{
		$criteria = $this -> giveTemporaryCriteria();
		$criteria -> compare('level',0);
		
		
		//$criteria=new CDbCriteria;
		
		
		$roots = self::model() -> findAll($criteria);
		$articles = array();
		
		$criteria = $this -> giveTemporaryCriteria();
		
		foreach($roots as $root){
			$articles = array_merge($articles, $root -> giveTree($criteria) );
		}
		//print_r($articles);
		/*$criteria=new CDbCriteria;
		
		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('verbiage',$this->verbiage,true);
		$criteria->compare('parent_id',$this->parent_id, true);
		$criteria->compare('text',$this->text,true);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('clinic_card',$this->clinic_card,true);
        $criteria->compare('keywords',$this->keywords,true);
        $criteria->compare('description',$this->description,true);*/
		
		/*return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));*/
		/**
		 * Add the articles without a parent
		 */
		
		$criteria = new CDbCriteria();
		$criteria -> compare('parent_id','0');
		$criteria -> addNotInCondition('level', array(0));
		$errorArticles = Articles::model() -> findAll($criteria);
		$articles = array_merge($articles, $errorArticles);
		return new CArrayDataProvider($articles);
	}
	/**
	 * @return array - an array of Articles Objects that are children to the specified one
	 */
	public function giveChildren($criteria = NULL){
		if (!$criteria) {
			$criteria = new CDbCriteria;
		}
		if ((!isset($this -> children))||(empty($this -> children))) {
			//$criteria = new CDbCriteria;
			$criteria -> compare('parent_id',$this -> id);
			$this -> children = self::model() -> findAll($criteria);
		}
		return $this -> children;
	}
	/**
	 * @return array - all articles that lie lower than this one and are related to it
	 */
	public function giveTree($criteria){
		$rez = array($this);
		//echo "tree of";
		//echo $this -> verbiage. ' - ' ;
		$children = $this -> giveChildren(clone $criteria);
		//echo count($children). "<br/>";
		//echo count($this -> giveChildren()).'<- children';
		foreach( $children as $child) {
			//echo count($rez).'before,';
			//echo count($child -> giveTree($criteria)).'size';
			
			$rez = array_merge($rez,$child -> giveTree(clone $criteria));
			//echo count($rez).'after,<br/>';
			//echo $child -> verbiage ."<br/>";
		}
		//echo count($rez).'<br/>';
		return $rez;
	}
	/*
	gives a list of all child articles
	*/
	public function GiveArticlesById($id = '0', $count = false)
	{
		$command = Yii::app() -> db -> createCommand();
		
		$command -> select = 'id, verbiage, name, text';
		$command -> from = '{{articles}}';
		$command -> where = 'parent_id=:pid';
		$command -> order = 'name ASC';
		$command -> bindValue(':pid', $id, PDO::PARAM_STR);
		
		$reader = $command -> query();
		if ($count) 
		{
			$countCommand = Yii::app() -> db -> createCommand("SELECT COUNT(*) FROM `tbl_articles` WHERE `parent_id`=:pid");
		}
		$articles = array();
		foreach($reader as $value)
		{
			$article = $value;
			if ($count)
			{
				$countCommand -> bindValue(':pid', $value['id'], PDO::PARAM_STR);
				//$countCommand = Yii::app() -> db -> createCommand("SELECT COUNT(*) FROM `tbl_articles` WHERE `parent_id`='".$value['id']."'");
				$article['c'] = $countCommand -> queryScalar();
				//$rez = $countCommand -> execute();
				
			} else {
				$article['c'] = 0;
			}
			$article['name'] = ucfirst($article['name']);
			//$article['text'] = $
			$articles[] = $article;
		}
		
		return $articles;
	}
	public function GenerateUrl()
	{
		$url = Yii::app() -> baseUrl;
		foreach ($this -> getParentList() as $parent)
		{
			$url .= '/'.$parent['verbiage'];
		}
		return $this -> verbiage;
	}
	//generates JSON object with article ids of given level
	public function GenerateParentList($level)
	{
		if ($level==-1)
		{
			return CJSON::encode(array(  
				'no'=>true,
			)); 
		} else {
			$criteria = new CDbCriteria;
			
			$criteria -> select = 'id, name';
			$criteria -> condition = 'level=:lev';
			$criteria -> order = 'name DESC';
			$criteria -> params = array(':lev' => $level);
			$parentList = CHtml::listData(Articles::model()->findAll($criteria),'id','name');
			//$parentList = Articles::model()->findAll($criteria);
			$ret = '';
			foreach ($parentList as $id => $name)
			{
				$ret .= CHtml::tag('option', array('value' => $id), CHtml::encode($name));
			}
			return CJSON::encode(array('parentList' => $ret));
		}
	}
	//function that generates an array of possible levels of an article
	public function getLevelArray()
	{
		$command = Yii::app()->db->createCommand('SELECT MAX(`level`) FROM {{articles}}');
		$max_level = $command -> queryScalar();
		$levelArray[0] = 'Корневая статья';
		for ($i = 1; $i <= $max_level + 1; $i++)
		{
			$levelArray[$i] = $i;
		}
		return $levelArray;
	}
	//Give article information
	public function giveArticleContent($verbiage)
	{
		$rez = array();
		$rez['article'] = self::model() -> findByAttributes(array('verbiage' => $verbiage));
		$rez['children'] = self::model() -> GiveArticlesById($rez['article']['id'], false);
		//$rez['parents'] = self::model() -> GiveParentList($rez['article'], $rez['article']['id']);
		return $rez;
	}
    // this is standard function for getting a model of the current class
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	//Give parent list
	public function GiveParentList($article = false, $id = -1)
	{
		$parents = array();
		if (!$article){
			$article = self::model() -> findByPk($id);
		}
		if (!$article){
			return array();
		}
		$par_id = $article['parent_id'];
		for ($i = $article['level'] - 1; $i >= 0; $i--)
		{
			$cur = self::model() -> findByPk($par_id);
			$parents[$i] = array('verbiage' => $cur['verbiage'], 'name' => $cur['name']);
			$par_id = $cur['parent_id'];
		}
		$parents[-1] = array('name' => 'Библиотека', 'vebiage' => '');
		return $parents;
	}
	public function giveModelByTriggerArray($triggers)
	{
		asort(array_filter($triggers));
		$string = implode(';', $triggers);
		/*$criteria = new CDbCriteria();
		$criteria -> compare('trigger_value_id', $string);
		$criteria */
		return self::model() -> findByAttributes(array('trigger_value_id' => $string, 'id_type' => ArticleType::model() -> getNumber('hlam')));
	}
	/**
	 *
	 */
	public function getParentLabel(){
		if ((!$this -> parent_id)&&($this -> level > 0)) {
			return "Ошибка! Уровень отличен от 0, но родитель не найден!";
		} else {
			return ArticleType::model() -> getText($this -> id_type);
		}
	}
}
