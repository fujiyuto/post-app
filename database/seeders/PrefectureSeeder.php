<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PrefectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //　リクエストヘッダー
        $header = [
            'Content-Type' => 'application/json',
            'apiKey'       => env('MLIT_DPF_API_KEY', '')
        ];

        // リクエストボディ
        $body = [
            'query' => '{prefecture{code_as_string,name}}'
        ];

        // 国土交通DPFに都道府県コード取得のリクエスト送信
        $response = Http::withHeaders($header)->post(env('MLIT_URL', ''), $body);

        // 都道府県データ取得
        $prefectures = $response->json()['data']['prefecture'];

        // データ挿入
        $insert_data = [];
        foreach ($prefectures as $pref) {
            $insert_data[] = [
                'code' => $pref['code_as_string'],
                'prefecture_name' => $pref['name'],
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),
            ];
        }
        DB::table('prefectures')->insert($insert_data);
    }
}
