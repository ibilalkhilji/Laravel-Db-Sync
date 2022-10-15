# Laravel Db Sync

![Laravel DB Sync](https://user-images.githubusercontent.com/14019618/195097554-4c50bff2-26a6-4c06-9a9a-d6639758a0b5.png)

## Introduction

Sync remote database to a local database or vice-versa

> **Note** this requires remote MySQL connection to sync the database, and that need to be defined in database.php

## Install

Install the package. <br/>
*Note: This extension works in Laravel 8 and above.*

```bash
composer require khaleejinfotech/laravel-db-sync
```

## Config

You can publish the config file with:

```
php artisan vendor:publish --provider="Khaleejinfotech\LaravelDbSync\LaravelDbSyncServiceProvider" --tag="laravel-db-sync"
``` 

## Database Connections

Set the remote database credentials in your `config/database.php` file

```
'connections' => [
    ...
    
    'mysql_remote' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ],
    
    ...
],
```

## Observing records

To observe changes in records and to sync with remote/local database you must `use Syncable` to your model, by default this package uses its
own observer, but you can replace it with your own having the same code of this observer and your custom logics.

You can replace the observer class from the `config/laravel-db-sync.php` file.

```
...
'observer_class' => \Khaleejinfotech\LaravelDbSync\Observers\ModelObserver::class,
...
```

> Note: observer method will only be triggered on eloquent model.

## Ignoring models

You can also create a base model with `use Syncable` and can extend your child models, in this case if you ever wanted to ignore a certain
model(s) you can ignore them by specifying in the `config/laravel-db-sync.php` file like this:

```
...
'ignore_models' => [
    // Models to be ignored if sync booted in base model.
    User::class,
],
...
```

## Defining targets/remote server(s).

After setting up connections in the `config/database.php` file you must have to add connection names where the data has to be synced.

```
'targets' => [
    // database connection names
    'mysql_remote',
],
```

> Note: Please specify only remote connections here else it may cause data duplication or may fall into an error.

## Handling file uploads of a model

If a model handle files then in the configuration file the columns of the model should be defined so that the files can be synced to the
local/remote servers.

```
...
    /*\App\Models\Model::class => [
        [
            'column_name' => 'photo',
            'remote_disks' => [
                // remote disks locations from filesystem config file.
                'ftp'
            ],
            'local_disk' => 'public',
            'upload_path_local' => 'folder',
            'upload_path_remote' => 'folder',
        ]
    ],*/
...
```

## Events

| Event             | Description |
|-------------------|---|
| NoTargetDefined   | Fired when no remote database defined in config `config/laravel-db-sync.php` file.   |
| SyncSuccess       | Fired when each job successfully processed.  |
| SyncFailed        | Fired when a job is not processed successfully.  |
| SyncUploadSuccess | Fired when a file upload job successfully processed. |
| SyncUploadFailed  | Fired when a file upload job is not processed successfully. | 

## Commands

| Command             | Description |
|-------------------|---|
| `sync:table`   | Creates migration file. |
| `sync:local`   | Creates jobs for local database records to be synced on remote.  |
| `sync:remote`  | Creates jobs for remote database records to be synced on local.  |

## Usage

### To sync local database to remote by running: <br/><br/>

This will create job for each record that need to be synced.

```bash
php artisan sync:local
```

After that you need run the queue

```bash
php artisan queue:work
```

### To sync remote database to local by running: <br/><br/>

This will create job for each record that need to be synced.

```bash
php artisan sync:remote
```

After that you need run the queue

```bash
php artisan queue:work
```

> ***Suggestion:*** configure scheduler for above commands to automate synchronization.
