<?php

declare(strict_types=1);
/**
 * Material Design 3 Theme
 *
 * A modern dark theme based on Material Design 3 (Material You)
 * with periwinkle accent colors and slate background.
 */

return [
    'name' => 'Material Design 3',
    'creator' => 'codPass',
    'version' => '1.0',
    'targetversion' => '4.0.0',
    'js' => [
        'bootstrap-material-datetimepicker.min.js',
        'material.min.js',
        'mdl-jquery-modal-dialog.min.js',
        'app-theme.min.js',
    ],
    'css' => [
        'fonts.min.css',
        'material.min.css',
        'material-custom.min.css',
        'mdl-datetimepicker.min.css',
        'mdl-jquery-modal-dialog.min.css',
        'selectize-custom.min.css',
        'toastr.min.css',
        'styles.min.css',
        'material-3.min.css',  // MD3 overrides - must be last
    ],
];
