<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MunicipalitySeeder extends Seeder
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
            'query' => '{municipalities{code_as_string,prefecture_code,name}}'
        ];

        // 国土交通DPFに都道府県コード取得のリクエスト送信
        $response = Http::withHeaders($header)->post(env('MLIT_URL', ''), $body);

        Log::debug($response->json());

        // 市区町村データ取得
        $municipalities = $response->json()['data']['municipalities'];

        // データ挿入
        $insert_data = [];
        foreach ($municipalities as $muni) {
            $insert_data[] = [
                'code' => $muni['code_as_string'],
                'prefecture_code' => Str::padLeft($muni['prefecture_code'], 2, '0'),
                'municipalities_name' => $muni['name'],
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),
            ];
        }
        DB::table('municipalities')->insert($insert_data);
    }
}
