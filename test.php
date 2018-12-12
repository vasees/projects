<?php

include 'revertPunctuationMarks.php';

echo $result = revertPunctuationMarks("Привет! Как твои дела?") . PHP_EOL;
echo $result = revertPunctuationMarks("HELLO! Как     твои дела?") . PHP_EOL;
echo $result = revertPunctuationMarks("Пр^ивет Как, тв,ои де-ла?") . PHP_EOL;
echo $result = revertPunctuationMarks("!&)#%*(?") . PHP_EOL;
echo $result = revertPunctuationMarks("124   ёёЁ! Как твои дЁёела?") . PHP_EOL;
