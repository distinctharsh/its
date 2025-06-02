<?php

namespace App\Console\Commands;

use App\Models\Inspector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class EncryptInspectorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:encrypt-inspector-data';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing inspectors data for specific columns';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $inspectors = Inspector::all();

    foreach ($inspectors as $inspector) {
        // Ensure data is encrypted only once
        if (!$this->isEncrypted($inspector->getRawOriginal('name'))) {
            $inspector->update([
                'name' => $inspector->name, // Triggers setNameAttribute() to encrypt
                'passport_number' => $inspector->passport_number, // Triggers setPassportNumberAttribute() to encrypt
                'unlp_number' => $inspector->unlp_number // Triggers setUnlpNumberAttribute() to encrypt
            ]);
        }
    }

    $this->info('Inspector data encrypted successfully.');
}

    private function isEncrypted($value)
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
