<?php

$access_logPath = $argv[1];  //путь к файлу access_log.php
//проверки на правильность аргументов командной строки
if ($argc != 2) {
	die('Неверное количество файлов. Укажите 1 файл access_log.php');
} else if (preg_match('~.*access_log.php~', $access_logPath) != 1) {
	die('Указан неверный путь  к файлу access_log.php');
}

function parse($pattern, $content) {
	preg_match_all($pattern, $content, $matches);
	return $matches[0];
}

$file = fopen($access_logPath, 'r');  //открытие файла
$content = fread($file, filesize($access_logPath)); //чтение содержимого файла
// количество просмотров
$viewsPattern = '~([0-9]{1,3}[\.]){3}[0-9]{1,3}~'; 
$viewsCount = count(parse($viewsPattern, $content));
// количество уникальных url
$urlsPattern = '~"(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?"~i';
$urlsCount = count(array_unique(parse($urlsPattern, $content)));
preg_match_all($urlsPattern, $content, $match);
// объем трафика
$trafficPattern = '~ [0-9]+ "~';
$sumTraffic = 0;
$traffic = parse($trafficPattern, $content);
foreach ($traffic as $value) {
	$sumTraffic += $value; 
}
// количество строк в файле
$linesPattern = '~$~m';
$linesCount = count(parse($linesPattern, $content));
//коды состояний
$statusCodesPattern = '~" [0-9]{3} ~';
$statusCodes = '';
$arrayOfCodes = array_count_values(parse($statusCodesPattern, $content));
foreach ($arrayOfCodes as $code => $count) {
	$statusCodes .= substr($code, 2, -1) . ': ' . $count . "\n\t\t";
}
//поисковые роботы
$gooleBot = count(parse('~Google~', $content));
$bingBot = count(parse('~bingbot~', $content));
$ramblerBot = count(parse('~StackRambler~', $content));
$yahooBot = count(parse('~Yahoo! Slurp~', $content));
$baiduBot = count(parse('~Baiduspider~', $content));
$yandexBot = count(parse('~Yandex~', $content));
$mailruBot = count(parse('~Mail.Ru_Bot~', $content));
//закрытие файла
fclose($file);

echo 
'{
	views: ' . $viewsCount . ',
	urls: ' . $urlsCount . ',
	traffic: ' . $sumTraffic . ',
	lines: ' . $linesCount . ',
	crawlers: {
		Google: ' . $gooleBot . ',
		Bing: ' . $bingBot . ',
		Rambler: ' . $ramblerBot . ',
		Yahoo: ' . $yahooBot . ',
		Baidu: ' . $baiduBot . ',
		Yandex: ' . $yandexBot . ',
		Mail.ru: ' . $mailruBot . '
	},
	statusCodes: {
		' . substr($statusCodes, 0, -2) . 
'	}
}';