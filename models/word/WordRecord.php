<?php

namespace app\models\word;

use Yii;
use yii\mongodb\ActiveRecord;

class WordRecord extends ActiveRecord
{
    public static function collectionName()
    {
        return 'word';
    }

    public function attributes()
    {
        return [
            '_id',
            'lang',
            'name',
            'snss',
            'conns',
            'atime',
            'conn_type', // Not in DB, only for render
        ];
    }
}