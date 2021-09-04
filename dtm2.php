<?php
header("Content-type:application/json");
include_once 'simple_html_dom.php';
$id = $_GET['id'];
define('url', "https://mandat.dtm.uz/Magistr/DetailsM/" . $id);
$ch = curl_init(url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($info == 200) {

    $html = str_get_html($res);
    $js = array(
        1 => ['name' => "Diplom ball"],
        2 => ['name' => "Mutaxassislik ball"],
        3 => ['name' => "Chet tili"],

    );

    foreach ($html->find('tbody')[1]->find('td') as $i => $td) {
        if ($i <= 2) {
            $js[$i + 1]['score'] = $td->plaintext;
        } elseif ($i == 4) {
            $js['score'] = $td->plaintext;
        }
    }

    $body = $html->find('tbody')[0];
    foreach ($body->find('tr') as $value) {
        $fan1 = [];
        foreach ($value->find('td') as $value2) {
            $fan1[] = $value2->plaintext;
        }
        $js['direct'][] = implode("|", $fan1);
    }
    $js['type'][] = $html->find('div[style="text-align: center;]')[1]->find('div[class="col-md-3"]')[0]->plaintext;
    $js['type'][] = $html->find('div[style="text-align: center;]')[1]->find('div[class="col-md-3"]')[1]->plaintext;
    $js['type'][] = $html->find('div[style="text-align: center;]')[1]->find('div[class="col-md-3"]')[2]->plaintext;
    $js["names"] = "F.I.O: " . trim($html->find('<h5[style="font-family: serif"]')[0]->find('strong')[0]->innertext);
    // $js['all_score'] = "Umumiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[4]->find('b')[0]->innertext);
    // $js['score'][] = "Milliy sertifikat ball / Olimpiada: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[3]->innertext);
    // $js['score'][] = "Ijodiy ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[2]->innertext);
    // $js['score'][] = "CEFR ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[1]->innertext);
    // $js['score'][] = "Imtiyoz ball: " . trim($html->find('table[class="table table-striped"]')[0]->find('tbody')[0]->find('td[style="text-align: center"]')[0]->innertext);
    $js['result'] = utf8_decode($html->find('div[class="col-12"]')[2]->find('div[class="card-body"]')[0]->plaintext);
    $js['answer_sheet'] = trim($html->find('a[class="btn btn-block btn-default"]')[0]->href);
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
        "&#x410;" => "A",
        "Ortga- Davlat granti - To'lov shartnoma - Tavsiya etilmagan" => "",

    ];
    foreach ($in as $key => $value) {
        $out = str_replace($key, $value, $out);
    }
    echo $out;
} elseif ($info == 302) {
    header(
        "Location: https://api.qalandar.uz/dtm3.php?id={$id}"
    );
} else {
    $js = [
        'error' => "Xatolik aniqlandi",
        'err' => $info,
    ];
    $out = rawurldecode(json_encode($js, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT));
    echo $out;

}
