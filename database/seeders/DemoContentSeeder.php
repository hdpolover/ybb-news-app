<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('newsletter_subscriptions')->insertOrIgnore([
            'id' => Str::uuid(),
            'tenant_id' => DB::table('tenants')->value('id'),
            'email' => 'subscriber@example.com',
            'status' => 'active',
            'frequency' => 'weekly',
            'unsubscribe_token' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
