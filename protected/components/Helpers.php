<?php

function resetId($model = NULL) {
    if ($model) { 
        $id = $model->primaryKey;
        $tablename = $model->tableSchema->name;
        $sql = "ALTER TABLE $tablename AUTO_INCREMENT = $id";
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->query(); 
        
        if (!$result)
            throw new CHttpException(404, СHtml::encode('Не удалось обновить ID в таблице $tablename.')); 
    }        
    return;
}
function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
function str2url($str) {
    // переводим в транслит
    $str = rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}
function RenderAssignButton ($model)
{
    if (!$model->useDocDoc) {
        ?>
        <div class="assign"><a href="<?php echo Yii::app()->baseUrl; ?>/assign"><span>Записаться на прием</span></a>
        </div>
        <?php
    } else {
        $id = "DDWidgetButton_" . $model->verbiage;
        Yii::app()->getClientScript()->registerScript("turn_on_widget_" . $id, "
         DdWidget({
          widget: 'Button',
          template: 'Button_common',
          pid: '9705',
          id: '" . $id . "',
          container: '" . $id . "',
          action: 'LoadWidget',
          city: 'msk'
        });
        ", CClientScript::POS_READY); ?>
        <div id="<?php echo $id; ?>"></div>
        <?php
    }
}
?>
