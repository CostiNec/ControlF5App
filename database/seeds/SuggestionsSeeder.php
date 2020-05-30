<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuggestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     */
    public static function parseDoom($url)
    {
        $ch = curl_init();
        $timeout = 100000;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        return $dom;
    }

    public static function letter ($letter)
    {
        $doom = self::parseDoom('https://www.dex.ro/list/'.$letter);

        foreach ($doom->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('class') == 'list') {
                DB::table('suggestions')->insert([
                    'keywords' => trim($div->textContent)
                ]);
            }
        }

    }

    public function run()
    {
        for ($letter = 'a'; $letter < 'z'; $letter++) {
                self::letter($letter);
        }
    }

}
