<?php

namespace app\views\helpers;

use Yii;

class Word
{

    public static function outWordLeft($word)
    {
        $out  = '';
        $out .= '<div class="w-card panel panel-default">';
        $out .= '<input type="hidden" class="w-id" value="' . $word['_id'] . '">';
        $out .= '<input type="hidden" class="w-lang" value="' . $word['lang'] . '">';
        $out .= '<div class="panel-body">';
        $out .= '<div class="w-title">';
        $out .= '<h3>' . $word['name'] . '</h3>';
        if ($word['lang'] != 'en') {
            $out .= '<span class="label label-white">' . ucfirst($word['lang']) . '</span>';
        }
        $out .= '<button class="btn-add-conn btn btn-default btn-sm" data-toggle="modal" data-target="#conn-modal"><span class="glyphicon glyphicon-share-alt"></span></button>';
        $out .= '</div>';
        $out .= '<div class="w-content">';
        foreach ($word['snss'] as $senseId => $sense) {
            $out .= self::outSense($word['_id'], $senseId, $sense);
        }
        $out .= '</div>';
        $out .= '<div class="w-btn-wrap"><button class="btn-add-sns btn btn-default btn-sm" data-toggle="modal" data-target="#sns-modal"><span class="glyphicon glyphicon-plus"></span></button></div></div></div>';
        return $out;
    }

    public static function outWordRight($word)
    {
        $out  = '';
        $out .= '<div id="w-' . $word['_id'] . '" class="w-card panel panel-default">';
        $out .= '<input type="hidden" class="w-id" value="' . $word['_id'] . '">';
        $out .= '<input type="hidden" class="w-lang" value="' . $word['lang'] . '">';
        $out .= '<div class="panel-body">';
        $out .= '<div class="w-title">';
        $out .= '<h3><a href="home?wid=' . $word['_id'] . '">' . $word['name'] . '</a></h3>';
        if ($word['lang'] != 'en') {
            $out .= '<span class="label label-white">' . ucfirst($word['lang']) . '</span>';
        }
        $out .= '<span class="label label-' . Yii::$app->params['connStyles'][$word['conn_data']['type']] . '">' . Yii::$app->params['connLabels'][$word['conn_data']['type']] . '</span>';
        $out .= '<button class="btn-w-content btn-collapse btn btn-default btn-sm" data-toggle="collapse" data-target="#w-' . $word['_id'] . '-content" aria-expanded="false" aria-controls="w-' . $word['_id'] . '-content"><span class="glyphicon glyphicon-triangle-bottom"></span></button>';
        $out .= '<button class="btn-edit-conn btn btn-default btn-sm" data-toggle="modal" data-target="#edit-conn-modal" f-sns="' . $word['conn_data']['f_sense'] . '" conn-type="' . $word['conn_data']['type'] . '" t-sns="' . $word['conn_data']['t_sense'] . '" t-snss="' . implode(',', array_keys($word['snss'])) . '"><span class="glyphicon glyphicon-pencil"></span></button>';
        $out .= '</div>';
        $out .= '<div id="w-' . $word['_id'] . '-content" class="w-content collapse">';
        foreach ($word['snss'] as $senseId => $sense) {
            $out .= self::outSense($word['_id'], $senseId, $sense);
        }
        $out .= '</div></div></div>';
        return $out;
    }

    public static function outSense($wordId, $senseId, $sense)
    {
        $sentenceAreaId = 'snt-' . $wordId . '-' . $senseId;

        $out  = '';
        $out .= '<div id="w-' . $wordId . '-' . $senseId . '" class="w-item">';
        $out .= '<div class="sns-btn-wrap">';
        $out .= '<button class="btn-edit-sns btn btn-default btn-sm" data-toggle="modal" data-target="#sns-modal"><span class="glyphicon glyphicon-pencil"></span></button>';
        if (!empty($sense['snts'])) {
            $out .= '<button class="btn-snt btn-collapse btn btn-default btn-sm" data-toggle="collapse" data-target="#' . $sentenceAreaId . '" aria-expanded="false" aria-controls="' . $sentenceAreaId . '"><span class="glyphicon glyphicon-triangle-bottom"></span></button>';
        }
        $out .= '</div>';
        $out .= '<div class="w-sns-name">' . str_replace('_', '.', $senseId) . '</div>';
        $out .= '<div class="w-sns-content">';
        $out .= '<div class="w-sns">';
        $out .= '<ul class="list-unstyled">';
        foreach ($sense['expl'] as $expl) {
            $out .= '<li>' . self::outExpl($expl) . '</li>';
        }
        $out .= '</ul></div>';
        $out .= '<div id="' . $sentenceAreaId . '" class="w-snt collapse">';
        $out .= '<ol>';
        foreach ($sense['snts'] as $sentence) {
            $out .= '<li>' . self::outSentence($sentence) . '</li>';
        }
        $out .= '</ol></div></div>';
        $out .= '<input type="hidden" class="w-sns-expl" value="' . self::outExplData($sense['expl']) . '">';
        $out .= '<input type="hidden" class="w-sns-snts" value="' . self::outSentenceData($sense['snts']) . '">';
        $out .= '</div>';
        return $out;
    }

    /**
     * Output single explaination as HTML
     */
    public static function outExpl($expl) {
        // Fixed phrase or expression
        // eg. <word expression> => <strong>word expression</strong>
        $out = preg_replace('/^<(.*)>$/', '<strong>$1</strong>', $expl);

        // Word group
        // eg. \[ word | word || word , word \]
        // \[   => <span class="w-grp">
        // \]   => </span>
        // word => <span>word</span>
        // |    => <span class="sep"></span>
        // ||   => <span class="sep"></span><span class="sep"></span>
        // ,    => <span class="weak"></span>
        $out = preg_replace('/\\\\\[[ ]?/', '<span class="w-grp">', $out);
        $out = preg_replace('/[ ]?\\\\\]/', '</span>', $out);
        $out = preg_replace('/[ ]?\|[ ]?/', '<span class="sep"></span>', $out);
        $out = preg_replace('/[ ]+,[ ]+/', '<span class="weak"></span>', $out);
        $out = preg_replace('/>([\w -]+)</', '><span>$1</span><', $out);
        
        // Common usage
        // eg. + trust, belief, faith => <span class="glyphicon glyphicon-plus"></span>trust, belief, faith
        $out = preg_replace('/\+[ ]?/', '<span class="glyphicon glyphicon-plus"></span>', $out);

        return $out;
    }

    /**
     * Output single sentence as HTML
     */
    public static function outSentence($sentence)
    {
        // Word appearence
        $out = preg_replace('/\\\\\[[ ]?/', '<u>', $sentence);
        $out = preg_replace('/[ ]?\\\\\]/', '</u>', $out);

        // Translation delimeter
        $out = preg_replace('/>>[ ]?/', '<br>', $out);

        return $out;
    }

    /**
     * Output explainations as plain text
     */
    public static function outExplData($expls)
    {
        $out = implode('\\n', $expls);
        $out = str_replace(['\\[', '\\]' ], ['[', ']'], $out);
        return $out;
    }

    /**
     * Output sentences as plain text
     */
    public static function outSentenceData($sentences)
    {
        $out = implode('\\n', $sentences);
        $out = str_replace(['\\[', '\\]' ], ['[', ']'], $out);
        return $out;
    }
}