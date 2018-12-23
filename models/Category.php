<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public function addCategory($data){
        $this->name = $data;
        $this->save();
        return true;
    }
}