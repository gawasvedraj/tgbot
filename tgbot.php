<?php
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;
use GuzzleHttp\Client;
$httpClient = new Client();

$bot = new Nutgram($_ENV['TOKEN']);

$bot->onCommand('start', function(Nutgram $bot) {
	$bot->sendMessage('Generic Start');
});

$bot->onCommand('start@gawasvedraj_bot', function(Nutgram $bot) {
	$bot->sendMessage('Special Start');
});

$bot->onText('gio {device}', function (Nutgram $bot, string $device) use($httpClient) {
	$repo = "ItsVixano-releases/LineageOS_{$device}";
	$apiUrl = "https://api.github.com/repos/{$repo}/releases/latest";

	try {
		$response = $httpClient->get($apiUrl);
		$release = json_decode($response->getBody(), true);

		if (empty($release['assets'])) {
			$bot->sendMessage("No assets found for {$device} in {$repo}");
			return;
		}

		$zipAssets = array_filter($release['assets'], function ($asset) {
			return str_ends_with($asset['name'], '.zip');
		});

		if (empty($zipAssets)) {
			$bot->sendMessage("No zip assets found for {$device} in {$repo}");
			return;
		}

		$asset = reset($zipAssets); // Get the first zip asset
		$message = "Latest LineageOS for {$device}:\n";
		$message .= "Version: {$release['tag_name']}\n";
		$message .= "Download: {$asset['browser_download_url']}";

		$bot->sendMessage($message);

	} catch (ClientException $e) {
		echo $e;
	} catch (\Exception $e) {
		$bot->sendMessage("$device not found");
	}
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
			$bot->sendMessage(text: embedMe($bot) . " " . $thing . "了 " . embedYou($bot) . " \!", parse_mode: MD());
		} else {
			$bot->sendMessage(text: embedMe($bot) . " " . $thing . "了自己 \!", parse_mode: MD());
		}
	} else {
		;
	}
});

$bot->onText('\\\{thing}', function(Nutgram $bot, string $thing) {
	if (!(mb_substr($thing, 0, 1) == " ")) {
		if ($bot->message()->reply_to_message != null) {
			$bot->sendMessage(text: embedMe($bot) . " 被 " . embedYou($bot) . " " . $thing . "了 \!", parse_mode: MD());
		} else {
			$bot->sendMessage(text: embedMe($bot) . " 被自己 " . $thing . "了 \!", parse_mode: MD());
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

$messagesReceived = [];

$bot->onMessage(function (Nutgram $bot) use (&$messagesReceived) {
	$message = $bot->message();

	if ($message->text) {
		$content = strtolower(trim($message->text));
		$contentType = 'text';
	} elseif ($message->sticker) {
		$content = $message->sticker->file_id;
		$contentType = 'sticker';
	} else {
		return;
	}

	$messagesReceived[] = $content;

	$count = array_count_values($messagesReceived);

	if (isset($count[$content]) && $count[$content] >= 3) {
		if ($contentType === 'text') {
			$bot->sendMessage($message->text);
		} elseif ($contentType === 'sticker') {
			$bot->sendSticker($content);
		}

		$messagesReceived = array_filter($messagesReceived, function($msg) use ($content) {
			return $msg !== $content;
		});
	}
});

$bot->run();
