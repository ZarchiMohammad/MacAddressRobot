<?php

require_once("config.php");
$json = file_get_contents('php://input');
$tg = Telegram::getInstance($json);
$cr = Core::getInstance();


$chatId = $tg->getChatId();
$text = $tg->getMessageText();

if ($tg->isChannelPost() === false && $tg->isEditChannelPost() === false) {

    if ($tg->pinnedMessage() != true) {
        switch ($text) {
            case "/start":
                $cr->setStartMenu($chatId, $text);
                break;
            default:
                $cr->setMacAddressMessage($chatId, $text);
                break;
        }
    }
}
