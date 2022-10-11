<?php
return [
    'observer_class' => \Khaleejinfotech\LaravelDbSync\Observers\ModelObserver::class,
    'ignore_models' => [
        // Models to be ignored if sync booted in base model.
    ],
    'targets' => [
        // database connection names
    ],
];
