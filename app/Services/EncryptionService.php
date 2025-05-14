<?php

namespace App\Services;

class EncryptionService {
    public function encrypt($data) {
        return encrypt($data);
    }

    public function decrypt($encryptedData) {
        return decrypt($encryptedData);
    }
}