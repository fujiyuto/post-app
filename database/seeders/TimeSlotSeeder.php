<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 時間（0〜23）、分（0, 15, 30, 45）の組み合わせ
        $insert_data = [];
        $minute_arr = [0, 15, 30, 45];
        for ($i = 0; $i <= 23; $i++) {
            foreach ($minute_arr as $min) {
                $insert_data[] = [
                    'hour'       => $i,
                    'minute'     => $min,
                    'created_at' => new Carbon(),
                    'updated_at' => new Carbon(),
                ];
            }
        }

        DB::table('time_slots')->insert($insert_data);
    }
}
