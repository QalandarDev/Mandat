<?php
/*

 ****Copyright 2021 Qalandar Axmedov
 ****(https://github.com/QalandarAxmedov)
 ****This file is part of Mandat
 ****(https://github.com/QalandarAxmedov/Mandat)
 ****Mandat is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 ****Mandat is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ****See the GNU Affero General Public License for more details.
 ****You should have received a copy of the GNU General Public License along with Mandat.
 ****If not, see <http://www.gnu.org/licenses/>.
Website: https://api.qalandar.uz/dtm.php?id=[ID]
 */

header("Content-type:application/json");
include_once 'simple_html_dom.php';
$id = $_GET['id'];
define('url', "https://mandat.dtm.uz/Home/Details/" . $id);
$ch = curl_init(url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($info == 200) {

    $html = str_get_html($res);
    $js = array();
    $ball = $html->find('tr[style="text-align: center; vertical-align:middle"]')[2];
    for ($i = 0; $i < 5; $i++) {
        $j = $i + 1;
        $js[$j]['fan'] = $html->find('td[colspan="2"]')[$i]->plaintext;
        $js[$j]['fan'] = str_replace("10", ": 10", $js[$j]['fan']);
        $js[$j]['fan'] = str_replace("20", ": 20", $js[$j]['fan']);
        $js[$j]['channel'] = "@AQ_TGDEV";
        $js[$j]['correct'] = trim($ball->find('td')[2 * $i]->innertext);
        $js[$j]['score'] = trim($ball->find('td')[2 * $i + 1]->innertext);

    }
    $body = $html->find('tbody')[0];
    foreach ($body->find('tr') as $value) {
        $fan1 = [];
        foreach ($value->find('td') as $value2) {
            $fan1[] = $value2->plaintext;
        }
        $js['direct'][] = implode("|", $fan1);
    }
    $js["name"] = "F.I.O: " . trim($html->find('<h5[style="font-family: serif"]')[0]->find('strong')[0]->innertext);
    $js['all_score'] = "Umumiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[4]->find('b')[0]->innertext);
    $js['score'][] = "Milliy sertifikat ball / Olimpiada: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[3]->innertext);
    $js['score'][] = "Ijodiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[2]->innertext);
    $js['score'][] = "CEFR ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[1]->innertext);
    $js['score'][] = "Imtiyoz ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[0]->innertext);
    $js['result'] = utf8_decode($html->find('div[class="col-12"]')[2]->find('div[class="card-body"]')[0]->plaintext);
    $js['answer_sheet'] = trim($html->find('a[class="btn btn-block btn-default"]')[1]->href);
    $out = json_encode($js, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    // $output = mb_convert_encoding($out, 'UTF-8', 'WINDOWS-1252');
    $in = [
        "  " => "",
        "\r\n" => "",
        "&#x27;" => "'",
        "&#x2019;" => "'",
        "&#x201C;" => "'",
        "&#x201D;" => "'",
        "&#x2018;" => "'",
        "Ortga- Davlat granti - To'lov shartnoma - Tavsiya etilmagan" => "",

    ];
    foreach ($in as $key => $value) {
        $out = str_replace($key, $value, $out);
    }
    echo $out;
} elseif ($info == 302) {
    header(
        "Location: https://api.qalandar.uz/dtm2.php?id={$id}"
    );
} else {
    $js = [
        'error' => "Xatolik aniqlandi",
        'err' => $info,
    ];
    $out = rawurldecode(json_encode($js, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT));
    echo $out;

}
