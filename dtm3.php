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
define('url', "https://mandat.dtm.uz/Transfer/DetailsP/" . $id);
$ch = curl_init(url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($info == 200) {

    $html = str_get_html($res);
    $tr = $html->find('tr[style="text-align: center; vertical-align:middle"]')[0];
    $js[1]['fan'] = $tr->find('td')[0]->plaintext;
    $js[2]['fan'] = $tr->find('td')[1]->plaintext;

    $tr = $html->find('tr[style="text-align: center; vertical-align:middle"]')[2];
    $js[1]['correct'] = $tr->find('td')[0]->plaintext;
    $js[1]['score'] = $tr->find('td')[1]->plaintext;

    $js[2]['correct'] = $tr->find('td')[2]->plaintext;
    $js[2]['score'] = $tr->find('td')[3]->plaintext;

    $js['score'] = $tr->find('td')[4]->plaintext;
    $body = $html->find('tbody')[0];
    foreach ($body->find('tr') as $value) {
        $fan1 = [];
        foreach ($value->find('td') as $value2) {
            $fan1[] = $value2->plaintext;
        }
        $js['direct'][] = implode("|", $fan1);
    }
    $js['type'][] = $html->find('div[style="text-align: center;]')[0]->find('div[class="col-md-3"]')[0]->plaintext;
    $js['type'][] = $html->find('div[style="text-align: center;]')[0]->find('div[class="col-md-3"]')[1]->plaintext;
    $js['type'][] = $html->find('div[style="text-align: center;]')[0]->find('div[class="col-md-3"]')[2]->plaintext;
    $js["Namess"] = "F.I.O: " . trim($html->find('<h5[style="font-family: serif"]')[0]->find('strong')[0]->innertext);
    // $js['all_score'] = "Umumiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[4]->find('b')[0]->innertext);
    // $js['score'][] = "Milliy sertifikat ball / Olimpiada: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[3]->innertext);
    // $js['score'][] = "Ijodiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[2]->innertext);
    // $js['score'][] = "CEFR ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[1]->innertext);
    // $js['score'][] = "Imtiyoz ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[0]->innertext);
    $js['result'] = utf8_decode($html->find('div[class="col-12"]')[1]->find('div[class="card-body"]')[0]->plaintext);
    // $js['answer_sheet'] = trim($html->find('a[class="btn btn-block btn-default"]')[0]->href);
    $out = json_encode($js, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    // $output = mb_convert_encoding($out, 'UTF-8', 'WINDOWS-1252');
    $in = [
        "  " => "",
        "/\r/\n" => "",
        "\r" => "",
        ")\r\n" => ")",
        "&#x27;" => "'",
        "&#x2019;" => "'",
        "&#x201C;" => "'",
        "&#x201D;" => "'",
        "&#x2018;" => "'",
        "&#x410;" => "A",
        "Ortga- Talab bajarildi - Talab bajarilmadi" => "",

    ];
    foreach ($in as $key => $value) {
        $out = str_replace($key, $value, $out);
    }
    echo $out;
} else {
    $js = [
        'error' => "Xatolik aniqlandi",
        'err' => $info,
    ];
    $out = json_encode($js, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT);
    echo $out;

}
