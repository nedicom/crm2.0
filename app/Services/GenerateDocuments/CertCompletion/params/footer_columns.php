<?php
/**
 * @var string $textF
 * @var string $textS
 * @var float|null $totalPrice
 */
return [
    [
        'column' => ['width' => 2.01, 'style' => ['borderColor' => 'ccc', 'bgColor' => 'ffffff', 'valign' => 'center']],
        'text' => ''
    ],
    [
        'column' => ['width' => 4.55, 'style' => ['borderColor' => 'ccc', 'bgColor' => 'ffffff', 'valign' => 'center']],
        'text' => ''
    ],
    [
        'column' => ['width' => 2.09, 'style' => ['borderColor' => 'ccc', 'bgColor' => 'ffffff', 'valign' => 'center']],
        'text' => ''
    ],
    [
        'column' => ['width' => 3.20, 'style' => ['bgColor' => 'f2f2f2', 'valign' => 'center']],
        'text' => [
            [
                'value' => $textF,
                'fontStyle' => ['name' => 'Times New Roman', 'size' => 11, 'bold' => true, 'color' => 'black'],
                'prStyle' => ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0],
            ],
            [
                'value' => $textS,
                'fontStyle' => ['name' => 'Times New Roman', 'size' => 9, 'bold' => false, 'italic' => true, 'color' => 'black'],
                'prStyle' => ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0],
            ],
        ]
    ],
    [
        'column' => ['width' => 2.15, 'style' => null],
        'text' => [
            [
                'value' => (isset($totalPrice)) ? $totalPrice : null,
                'fontStyle' => [],
                'prStyle' => [],
            ],
        ]
    ],
    [
        'column' => ['width' => 2.50, 'style' => null],
        'text' => [
            [
                'value' => (isset($totalPrice)) ? $totalPrice : null,
                'fontStyle' => [],
                'prStyle' => [],
            ],
        ]
    ],
];
