<?php

    namespace app\models;

    use lithium\data\Model;

    class Users extends Model
    {
        public function setName($value) {
            var_dump($value); die;
        }
    }