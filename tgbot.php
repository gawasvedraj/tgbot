<?php
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;

$bot = new Nutgram($_ENV['TOKEN']);

$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage('Generic Start');
});

$bot->onCommand('start@gawasvedraj_bot', function(Nutgram $bot) {
    $bot->sendMessage('Special Start');
});

$bot->onCommand('code', function(Nutgram $bot) {
    $bot->sendMessage('You can view the source code at https://github.com/gawasvedraj/tgbot');
});

function embedMe(Nutgram $bot) {
    return "[" . $bot->user()->first_name . "](tg://user?id=" . $bot->user()->id . ")";
}

function embedYou(Nutgram $bot) {
    return "[" . $bot->message()->reply_to_message->from->first_name . "](tg://user?id=" . $bot->message()->reply_to_message->from->id . ")";
}

function MD() {
    return "MarkdownV2";
}

$bot->onText('你好', function(Nutgram $bot) {
    $bot->sendMessage(text: "你好 " . embedMe($bot), parse_mode: MD());
});

$bot->onText('//{thing}', function(Nutgram $bot, string $thing) {
    if (!(mb_substr($thing, 0, 1) == " ")) {
        if ($bot->message()->reply_to_message != null) {
		$bot->sendMessage(text: embedMe($bot) . " " . $thing . " 了 " . embedYou($bot) . " \!", parse_mode: MD());
	} else {
		$bot->sendMessage(text: embedMe($bot) . " " . $thing . " 了自己 \!", parse_mode: MD());
	}
    } else {
        ;
    }
});

$bot->onText('\\\{thing}', function(Nutgram $bot, string $thing) {
    if (!(mb_substr($thing, 0, 1) == " ")) {
        if ($bot->message()->reply_to_message != null) {
            $bot->sendMessage(text: embedMe($bot) . " 被 " . embedYou($bot) . " " . $thing . " 了 \!", parse_mode: MD());
	} else {
		$bot->sendMessage(text: embedMe($bot) . " 被自己 " . $thing . " 了 \!", parse_mode: MD());
	}
    } else {
        ;
    }
});

$bot->onText('nya', function(Nutgram $bot) {
    $bot->sendSticker("CAACAgUAAyEFAASDbMrDAAIyH2dP-MVnH8jzvb-bNfWmkXft5lLnAAIeAwAC80zgV1CcB5tlUSWfNgQ");
});

$bot->onText('sleepy', function(Nutgram $bot) {
    $bot->sendSticker("CAACAgUAAyEFAASNP1EeAAIDQmdzTNB97tO7vzpdLW8uvZAFB8dNAALgAgAC7gjgVzs-ZOlTzStENgQ");
});

$bot->run();

