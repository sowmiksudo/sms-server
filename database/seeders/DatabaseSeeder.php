<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('channels')->insert([
            'name' => '0xApp',
        ]);

        DB::table('settings')->insert([
            'key' => 'current_api',
            'value' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'max_retry',
            'value' => 2,
        ]);

        DB::table('apis')->insert([
            'method' => 'get',
            'url'=>'http://sms.bdtask.com/smsapi?api_key=C200841060d07abfcf6b00.15108031&type=unicode&senderid=8809612436202&contacts=0{number}&msg={message}',
            'retry_attempt'=>0,
            'validator'=>"SMS SUBMITTED",
            'expired_at'=>null
        ]);

        DB::table('balances')->insert([
            'channel_id' => '1',
            'balance' => '1000.00',
        ]);
    }
}
