<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;



class FirebaseServices
{
    protected Database $database;

    public function __construct()
    {
        $this->database = $this->initDatabase();
    }

    private function initDatabase(): Database
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials_file'))
                ->withDatabaseUri(config('firebase.database_uri'));



            return $factory->createDatabase();
        } catch (FirebaseException $e) {
            throw new \Exception("Lỗi kết nối Firebase: " . $e->getMessage());
        }
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }
}
