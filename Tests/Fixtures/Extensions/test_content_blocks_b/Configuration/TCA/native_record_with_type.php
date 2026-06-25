<?php

return [
    'ctrl' => [
        'title' => 'Native record with type',
        'label' => 'title',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'type' => 'type',
    ],
    'types' => [
        'type_1' => [
            'showitem' => 'title',
        ],
        'type_2' => [
            'showitem' => 'title',
        ],
    ],
    'columns' => [
        'type' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Type 1', 'value' => 'type_1'],
                    ['label' => 'Type 2', 'value' => 'type_2'],
                ],
            ],
        ],
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
];
