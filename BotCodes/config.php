<?php

require_once("system/Telegram.php");
require_once("system/Database.php");
require_once("system/Core.php");

const _TOKEN = "--BotToken-- ";
const _ADMIN = "--Admin--";

const _PROJECTS_CHANNEL = "--ChannelChatId--";
const _REPORT_CHANNEL = "--ChannelChatId--";

global $config;
$config['host'] = "localhost";
$config['user'] = "--username--";
$config['pass'] = "--password--";
$config['name'] = "--DatabaseName--";
