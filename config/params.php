<?php

return [
    'adminEmail' => 'admin@example.com',
    // Languages
    'langs' => [
        'en',
        'fr'
    ],
    // Connection type => reverse connection type
    // 'Explained by ->',
    // 'Explain ->',
    // 'Sematically contain ->',
    // 'Sematically belong to ->',
    // 'Literally contain ->',
    // 'Literally belong to ->',
    // 'Differentiation',
    // 'Collocation',
    'connTypes' => [
        'synonym'      => 'synonym',
        'antonym'      => 'antonym',
        'verb_object'  => 'object_verb',
        'object_verb'  => 'verb_object',
        'subject_verb' => 'verb_subject',
        'verb_subject' => 'subject_verb',
        'adj_subject'  => 'subject_adj',
        'subject_adj'  => 'adj_subject',
        'association'  => 'association',
        'translation'  => 'translation',
        'cognate'      => 'cognate',
    ],
    // Connection labels
    'connLabels' => [
        'synonym'      => 'Synonym',
        'antonym'      => 'Antonym',
        'verb_object'  => 'Object of action',
        'object_verb'  => 'Action on object',
        'subject_verb' => 'Action to do',
        'verb_subject' => 'Subject of action',
        'adj_subject'  => 'Describe',
        'subject_adj'  => 'Described by',
        'association'  => 'Association',
        'translation'  => 'Translation',
        'cognate'      => 'Cognate'
    ],
    // Label colors: red, orange, yellow, green, cyan, blue, purple, grey
    'connStyles' => [
        'synonym'      => 'green',
        'antonym'      => 'red',
        'verb_object'  => 'blue',
        'object_verb'  => 'blue',
        'subject_verb' => 'blue',
        'verb_subject' => 'blue',
        'adj_subject'  => 'yellow',
        'subject_adj'  => 'yellow',
        'association'  => 'cyan',
        'translation'  => 'cyan',
        'cognate'      => 'purple',
    ],
    'colors' => [
        'red'    => '#D9534F',
        'orange' => '#F37735',
        'yellow' => '#FFC425',
        'green'  => '#5CB85C',
        'cyan'   => '#21AABD',
        'blue'   => '#107FC9',
        'purple' => '#6E5494',
        'grey'   => '#777777',
        'black'  => '#333333',
    ],
];
