<?php
return [
    'observer_class' => \Khaleejinfotech\LaravelDbSync\Observers\ModelObserver::class,
    'ignore_models' => [
        // Models to be ignored if sync booted in base model.
    ],
    'targets' => [
        // database connection names
    ],
    'has_uploads' => [
        // array of models that has files
        // each model can have multiple upload fields, like given example
        /*\App\Models\RegistrationForm::class => [
            [
                'column_name' => 'photo',
                'remote_disks' => [
                    // remote disks locations from filesystem config file.
                    'ftp'
                ],
                'upload_path_local' => public_path('storage/forms'), // absolute path
                'upload_path_remote' => 'storage/forms', // relative path
            ]
        ],*/
    ],
];
