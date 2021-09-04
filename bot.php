<?php
include "lib/uz.telebot.php";
require_once __DIR__ . '/myconfig.php';
$updates = file_get_contents("php://input");
$bot = new uz_telebot($CFG->token1, $updates, $CFG->db);
$msg = $bot->message;
$cq = $bot->callback_query;
$keySub = inlineButton([
    buttonRow(
        buttonUrl(
            "📝Kanalga a'zo bo'lish",
            $CFG->channel_url
        )
    ),
    buttonRow(
        buttonCallback("✅Tasdiqlash✅", "cmdcheck")
    ),
]);
if ($msg) {
    $bot->GET_ROW("SELECT first_name FROM dtmUser WHERE id=? LIMIT 1", [
        $msg->chat->id,
    ]);
    if (!$bot->result) {
        $bot->QUERY(
            "INSERT INTO dtmUser (first_name,last_name,userlink,id)
        VALUES(?,?,?,?)",
            [
                $msg->from->first_name,
                $msg->from->last_name,
                $msg->from->username,
                $msg->from->id,
            ]
        );
    }
    if (!$bot->is_member($CFG->channel_id, $msg->from->id)) {

        $bot->send_message(
            $msg->chat->id,
            "\n\nBotdan to'liq foydalanish uchun kanalga a'zo bo'lish shart!\n\nA'zo bo'lgach Tasdiqlash tugmasini bosing",
            $keySub
        );
        exit();
    }
    if ($msg->text === "/start") {
        $bot->send_message($msg->from->id, "Mandat natijangizni bilish uchun id raqamingizni yuboring");
    } elseif (strlen($msg->text) > 5) {
        $data = json_decode(file_get_contents($CFG->dtmurl . $msg->text), true);
        if (array_key_exists('name', $data)) {
            $text = _italic("Bakalavr-2021 | Natija") .
            "\n\n" . _italic($data['name']) .
            "\nID: " . _bold($msg->text) .
                "\n\n";
            for ($i = 1; $i <= 10; $i++) {
                if (array_key_exists($i, $data)) {
                    $text .= _italic($data[$i]['fan'] . ": " . $data[$i]['score'] . "(✅" . $data[$i]['correct'] . ")\n");
                }
            }
            $text .= "\n🏢" . _bold(implode("\n@MandatUrDUbot\n🏢", $data['direct'])) . ".\n\n";
            $text .= "📈" . implode("\n📈 ", $data['score']) .
            "\n\n📌Natija: " . _bold($data['result']) .
            "\n\n📊" . _bold($data['all_score']) .
            "\n\n" . _a("Batafsil", $data['answer_sheet']) .
            "\n\n©️ @MandatUrDUbot\n®️ " . _code("mandat.dtm.uz");
            $bot->send_message($msg->from->id, $text);
        } elseif (array_key_exists('names', $data)) {
            $text = _italic("Magistr-2021 | Natija") .
            "\n\n" . _italic($data['names']) .
            "\n" . _bold(implode("\n", $data['type'])) .
                "\n\n";
            for ($i = 1; $i <= 10; $i++) {
                if (array_key_exists($i, $data)) {
                    $text .= _italic($data[$i]['name'] . ": " . $data[$i]['score'] . "\n");
                }
            }
            $text .= "\n🏢" . _bold(implode("\n🏢", $data['direct'])) .
            "\n@MandatUrDUbot\n\n📌Natija: " . _bold($data['result']) .
            "\n\n📊 Umumiy ball: " . _bold($data['score']) .
            "\n\n" . _a("Batafsil", $data['answer_sheet']) .
            "\n\n©️ @MandatUrDUbot\n®️ " . _code("mandat.dtm.uz");
            $bot->send_message($msg->from->id, $text);
        } elseif (array_key_exists("Namess", $data)) {
            $text = _italic("O'qishni ko'chirish-2021 | Natija") .
            "\n\n" . _italic($data['Namess']) .
                "\n\n";
            for ($i = 1; $i <= 10; $i++) {
                if (array_key_exists($i, $data)) {
                    $text .= _italic($data[$i]['fan'] . ": " . $data[$i]['score'] . "(✅" . $data[$i]['correct'] . ")\n");
                }
            }
            $text .= "\n🏢" . _bold(implode("\n@MandatUrDUbot\n🏢", $data['direct'])) . ".\n\n";
            $text .= "\n\n📌Natija: " . _bold($data['result']) .
            "\n\n📊 Umumiy ball: " . _bold($data['score']) .
            "\n\n©️ @MandatUrDUbot\n®️ " . _code("mandat.dtm.uz");
            $bot->send_message($msg->from->id, $text);

        } else {
            if ($data['err'] == 302) {
                $bot->send_message($msg->from->id, "Kiritilgan ID:{$smg->from->id} bo'yicha mandat.dtm.uz saytida ma'lumot topilmadi");
            } else {
                $bot->send_message($msg->from->id, "Error: {$data['err']}\nMandat.dtm.uz javob bermadi\n1 daqiqadan so'ng qaytadan urinib ko'ring");
            }
        }
    }
}
if ($cq) {
    $isSub = $bot->is_member($CFG->channel_id, $cq->from->id);
    if (!$isSub) {
        $bot->answerCallbackQuery(
            $cq->id,
            "Avval kanalga a'zo bo'ling, keyin Tasdiqlash tugmasi faollashadi",
            true
        );
    } else {
        $bot->answerCallbackQuery($cq->id, "A'zolik tasdiqlandi", true);
        $bot->editmessage(
            $cq->from->id,
            $cq->message->message_id,
            "Mandat natijangizni bilish uchun id raqamingizni yuboring"
        );
    }
}
