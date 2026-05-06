<?php

use Illuminate\Support\Facades\Crypt;

if (!function_exists('encrypt_id')) {
    /**
     * Encrypt a given ID.
     *
     * @param mixed $id
     * @return string|null
     */
    function encrypt_id($id)
    {
        try {
            return encrypt($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('decrypt_id')) {
    /**
     * Decrypt a given encrypted ID.
     *
     * @param string $encryptedId
     * @return mixed|null
     */
    function decrypt_id($encryptedId)
    {
        try {
            return decrypt($encryptedId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
