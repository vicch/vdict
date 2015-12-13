<?php

namespace app\controllers\components;

use Yii;
use app\models\word\WordRecord;

/**
 * Reusable code among controllers
 */
class WordComponent
{
    public static function addWord($data)
    {
        $word = new WordRecord;
        $word->lang  = $data['lang'];
        $word->name  = $data['name'];
        $word->snss  = isset($data['snss']) ? $data['snss'] : [];
        $word->conns = isset($data['conns']) ? $data['conns'] : [];
        $word->atime = date('Y/m/d H:i:s', time());

        if ($word->insert() === true) {
            return $word;
        } else {
            return false;
        }
    }

    /**
     * Parse senses input:
     *
     * <senseId>
     * <expl>
     * ...
     *     <sentence>
     *     ...
     *
     * <senseId>
     * ...
     */
    public static function parseSenses()
    {
        $text = Yii::$app->request->post('snss', '');
        $senses = [];

        if (empty($text))
            return $senses;

        $text   = str_replace(['[', ']'], ['\\[', '\\]' ], $text);
        $groups = explode("\n\n", $text);

        foreach ($groups as $group) {
            $lines = explode("\n", $group);
            $senseId = str_replace('.', '_', array_shift($lines));
            $expl = [];
            $snts = [];
            foreach ($lines as $line) {
                // Sentences start with spaces, because Evernote does not support tabs
                // Good job, Evernote
                if (preg_match('/^[ ]+/', $line)) {
                    $snts[] = trim($line);
                } else {
                    $expl[] = trim($line);
                }
            }
            $senses[$senseId] = [
                'expl' => $expl,
                'snts' => $snts,
            ];
        }
        return $senses;
    }
}