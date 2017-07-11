<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'DevelopmentKBox' => [
        'path' => 'dev_kbox',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'DevelopmentSusuan' => [
        'path' => 'dev_susuan',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'DevelopmentWordtribe' => [
        'path' => 'dev_wordtribe',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'BetaSusuan' => [
        'path' => 'beta_susuan',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'ProductionKbox' => [
        'path' => 'prod_kbox',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'ProductionSusuan' => [
        'path' => 'prod_susuan',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],
    'ProductionWordTribe' => [
        'path' => 'prod_wordtribe',
        'setWritable' => [
            'questionmis/runtime',
            'questionmis/web/assets',
            'console/runtime'
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'questionmis/config/main-local.php',
        ],
    ],

];
