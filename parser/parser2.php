<?php

ini_set('memory_limit', '-1');  //отключение ограничений на использование памяти скриптом

$access_logPath = $argv[1];  //путь к файлу access_log.php
//проверки на правильность аргументов командной строки
if ($argc != 2) {
	die('Неверное количество файлов. Укажите 1 файл access_log.php');
} else if (preg_match('~^(.*/)?access_log.php$~', $access_logPath) != 1) {
	die('Указан неверный файл. Укажите файл access_log.php');
} else if (!file_exists($access_logPath)) {
	die('Указан неверный путь к файлу');
}

function parse($pattern, $content) {
	preg_match_all($pattern, $content, $matches);
	return $matches[0];
}
//  чтение содержимого access_log.php
$file = fopen($access_logPath, 'r');
$content = fread($file, filesize($access_logPath));
fclose($file);
// количество просмотров
$viewsPattern = '~([0-9]{1,3}[\.]){3}[0-9]{1,3}~'; 
$viewsCount = count(parse($viewsPattern, $content));
echo '{' . "\n\t" . 'views: ' . $viewsCount . ',' . "\n\t";
// количество уникальных url
$urlsPattern = '~"(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?"~i';
$urlsCount = count(array_unique(parse($urlsPattern, $content)));
echo 'urls: ' . $urlsCount . ',' . "\n\t";
// объем трафика
$trafficPattern = '~ [0-9]+ "~';
$sumTraffic = 0;
$traffic = parse($trafficPattern, $content);
foreach ($traffic as $value) {
	$sumTraffic += $value; 
}
echo 'traffic: ' . $sumTraffic . ',' . "\n\t";
// количество строк в файле
$linesPattern = '~$~m';
$linesCount = count(parse($linesPattern, $content));
echo 'lines: ' . $linesCount . ',' . "\n\t";

//поисковые роботы
echo 'crawlers: {' . "\n";
$gooleBot = count(parse('~Google~', $content));
echo "\t\t" . 'Google: ' . $gooleBot . ',' . "\n";
$bingBot = count(parse('~bingbot~', $content));
echo "\t\t" . 'Bing: ' . $bingBot . ',' . "\n";
$ramblerBot = count(parse('~StackRambler~', $content));
echo "\t\t" . 'Rambler: ' . $ramblerBot . ',' . "\n";
$yahooBot = count(parse('~Yahoo! Slurp~', $content));
echo "\t\t" . 'Yahoo: ' . $yahooBot . ',' . "\n";
$baiduBot = count(parse('~Baiduspider~', $content));
echo "\t\t" . 'Baidu: ' . $baiduBot . ',' . "\n";
$yandexBot = count(parse('~Yandex~', $content));
echo "\t\t" . 'Yandex: ' . $yandexBot . ',' . "\n";
$mailruBot = count(parse('~Mail.Ru_Bot~', $content));
echo "\t\t" . 'Mail.ru: ' . $mailruBot . "\n\t" . '},' . "\n\t";

//коды состояний
echo 'statusCodes: {' . "\n\t\t";
$statusCodesPattern = '~" [0-9]{3} ~';
$statusCodes = '';
$arrayOfCodes = array_count_values(parse($statusCodesPattern, $content));
foreach ($arrayOfCodes as $code => $count) {
	$statusCodes .= substr($code, 2, -1) . ': ' . $count . "\n\t\t";
}		
echo substr($statusCodes, 0, -2) . "\t" . '}' . "\n" . '}';