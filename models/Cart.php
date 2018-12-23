<?php

namespace app\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord
{
    public function addToCart($id_cart, $id_product, $amountOfProducts){
     $this->id = $id_cart;
     $this->id_product = $id_product;
     $this->amount_products = $amountOfProducts;
     $this->save();
     return true;
    }
}