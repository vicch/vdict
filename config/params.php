<?php

return [
    'adminEmail' => 'admin@example.com',
    // Languages
    'langs' => [
        'en',
        'fr'
    ],
    // Connection type => reverse connection type
    // 'Translation',
    // 'Cognate',
    // 'Explained by ->',
    // 'Explain ->',
    // 'Sematically contain ->',
    // 'Sematically belong to ->',
    // 'Literally contain ->',
    // 'Literally belong to ->',
    // 'Differentiation',
    // 'Association',
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
    ],
    // Connection labels
    'connLabels' => [
        'synonym'      => 'Synonym',
        'antonym'      => 'Antonym',
        'verb_object'  => 'Object',
        'object_verb'  => 'Action',
        'subject_verb' => 'Do action',
        'verb_subject' => 'Subject',
        'adj_subject'  => 'Describe',
        'subject_adj'  => 'Described by',
        'association'  => 'Association',
        'translation'  => 'Translation',
    ],
    // Label colors: red, orange, yellow, green, cyan, blue, pink, grey
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
    ],
];
