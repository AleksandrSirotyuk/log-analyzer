<?php
$this->title = 'Главная';

use yii\widgets\ActiveForm;
use yii\helpers\Url;

//print_r($actions);
//print_r($namesOfCategories[2]['name']);
//print_r($category_id);
//print_r($prevAction['scanned_product_id']);
//for($i=0; $i<count($actions); $i++) {
//    preg_match('|goods|', $actions[$i]['link'], $res);
//        print_r($res);
//}

//print_r($success_transactions);
//echo '<br><br><br>';
//print_r($actions);
//echo '<br><br><br>';
//echo count($actions);
//echo '<br><br><br>';
//echo count($success_transactions);
?>
<h2>Анализатор логов</h2>
<a href="<?= Url::to(['/site/updating-tables']) ?>">Добавить в базу данных все записи/обновить базу данных</a><br/>
<a href="<?= Url::to(['/site/updating-tables-for-testing']) ?>">Добавить в базу данных небольшое количество записей для тестирования</a><br/>
<a href="<?= Url::to(['/site/clear-tables']) ?>">Очистить таблицы</a><br/>
<a href="<?= Url::to(['/report1']) ?>">Получить отчет №1 (количество брошенных корзин за определенный период времени)</a><br/>
<a href="<?= Url::to(['/report2']) ?>">Получить отчет №2 (количество пользователей, совершавших повторные покупки за определенный период времени)</a><br/>
<a href="<?= Url::to(['/report3']) ?>">Получить отчет №3 (нагрузка на сайт за астрономический час)</a><br/>

