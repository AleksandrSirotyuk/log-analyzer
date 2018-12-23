<?php


namespace app\models;

use yii\base\Model;


class Report2Form extends Model
{
    public $date_from;
    public $date_to;
    public function attributeLabels()
    {
        return [
            'date_from' => 'с (формат ввода: YYYY-MM-DD HH:MM:SS)',
            'date_to' => 'до (формат ввода: YYYY-MM-DD HH:MM:SS)',
        ];
    }
    public function rules()
    {
        return [
            [['date_from', 'date_to'], 'safe']
        ];
    }
}