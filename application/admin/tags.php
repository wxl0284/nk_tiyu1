<?php
/**
 * 测试1.0 [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://测试1.0.yuan1994.com/
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

return [
    'module_init' => [
        '\\app\\common\\behavior\\WebLog',
    ],
    'app_end' => [
        '\\app\\common\\behavior\\WebLog',
    ]
];
