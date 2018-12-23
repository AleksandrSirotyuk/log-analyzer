<?php

namespace app\models;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public function addProduct($data, $id)
    {
        $this->name = $data;
        $this->id_category = $id;
        $this->save();
        return true;
    }
}
