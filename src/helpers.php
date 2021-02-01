<?php

use Illuminate\Support\Facades\DB;

if (! function_exists('db_encrypt')) {
    /**
     * Encrypt value.
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Query\Expression
     */
    function db_encrypt($value)
    {
        $key = config('mysql-encrypt.key');

        if (config('database.default') === 'sqlsrv') {
            return DB::raw("EncryptByPassPhrase('{$key}', '{$value}')");
        }

        return DB::raw("AES_ENCRYPT('{$value}', '{$key}')");
    }
}

if (! function_exists('db_decrypt')) {
    /**
     * Decrpyt value.
     *
     * @param  mixed $column
     * @return \Illuminate\Database\Query\Expression
     */
    function db_decrypt($column)
    {
        $key = config('mysql-encrypt.key');

        if (config('database.default') === 'sqlsrv') {
            return DB::raw("CAST(DecryptByPassPhrase('{$key}', [{$column}]) AS VARCHAR(255)) AS '{$column}'");
        }

        return DB::raw("AES_DECRYPT({$column}, '{$key}') AS '{$column}'");
    }
}


if (! function_exists('db_decrypt_string')) {
    /**
     * Decrpyt value.
     *
     * @param  string  $column
     * @param  string  $value
     * @param  string  $operator
     * @return string
     */
    function db_decrypt_string($column, $value, $operator = 'LIKE')
    {
        if (config('database.default') === 'sqlsrv') {
            return sprintf(
                "CAST(DecryptByPassPhrase('%s', [%s]) AS VARCHAR(300)) %s '%s'",
                config("mysql-encrypt.key"),
                $column,
                $operator,
                $value
            );
        }

        return 'AES_DECRYPT('.$column.', "'.config("mysql-encrypt.key").'") '.$operator.' "'.$value.'" COLLATE utf8mb4_general_ci';
    }
}
