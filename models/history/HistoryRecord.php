<?php

namespace app\models\history;

use Yii;
use yii\mongodb\ActiveRecord;

class HistoryRecord extends ActiveRecord
{
    public static function collectionName()
    {
        return 'history';
    }

    public function attributes()
    {
        return [
            '_id',
            'wid',
            'lang',
            'name',
            'time',
        ];
    }
}