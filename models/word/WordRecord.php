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
            'lang',      // Language
            'name',      // Word itself, use 'name' to differentiate from the 'word' entity
            'snss',      // Senses
            'conns',     // Connections
            'atime',     // Added time
            'conn_data', // Connection data, not in DB, but used in rendering
        ];
    }
}