<?php

class Core
{

    private static $cr;
    private static $db;
    private static $tg;

    public static function getInstance()
    {
        if (self::$cr == null) {
            self::$cr = new Core();
        }
        return self::$cr;
    }

    public function __construct()
    {
        self::$db = Database::getInstance();
        self::$tg = Telegram::getInstance();
    }

    public function sendUserEntrance($chatId, $entrance, $function)
    {
        $message = "User: <code>" . $chatId . "</code>\n";
        $message .= "Name: <code>" . self::$tg->getFirstName() . " " . self::$tg->getLastName() . "</code>\n";
        $message .= "Profile: <a href='tg://user?id=" . $chatId . "'>Click To Go</a>" . "\n";
        $message .= "Entrace: <code>" . $entrance . "</code>" . "\n";
        $message .= "Function: <code>" . $function . "</code>" . "\n";
        $time = $this->getTimeStamp(true);
        $message .= "TimeStamp: <code>" . $time . "</code>\n";
        self::$tg->sendMessage(_REPORT_CHANNEL, $message);
        self::$db->insertEntrance($chatId, $time, $entrance);
    }

    public function setStartMenu($chatId, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $text, __FUNCTION__);
        self::$db->insertUserData($chatId);
        $message = "Send your MAC address and get the name of the vendor, acceptable formats: \n";
        $message .= "• <code>00-11-22-33-44-55</code> \n";
        $message .= "• <code>00:11:22:33:44:55</code> \n";
        $message .= "• <code>00.11.22.33.44.55</code> \n";
        $message .= "• <code>001122334455</code> \n";
        $message .= "• <code>0011.2233.4455</code> \n";
        $data = self::$tg->sendMessage($chatId, $message);
        $json = json_decode($data);
        $messageId = $json->result->message_id;
        self::$tg->unpinChatMessage($chatId);
        self::$tg->pinChatMessage($chatId, $messageId);
    }

    public function setMacAddressMessage($chatId, $macAddress)
    {

        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $macAddress, __FUNCTION__);

        $url = "https://api.macvendors.com/" . urlencode($macAddress);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (TelegramBot:@MacAddressRobot | Channel:@ZarchiProjects | Link:https://t.me/ZarchiProjects) Firefox/83.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $json = json_decode($response);
        if (isset($json->errors)) {
            self::$tg->sendMessage($chatId, "Not Found");
        } else {
            self::$tg->sendMessage($chatId, "Vendor: " . $response);
        }


    }

    /*     * * * * * * * * * * * * * * * * * * * * * *
     * ╔═╗╔═╗  ╔╗╔╗             ╔╗╔╗      ╔╗      *
     * ║║╚╝║║ ╔╝╚╣║            ╔╝╚╣║      ║║      *
     * ║╔╗╔╗╠═╩╗╔╣╚═╦══╦═╗ ╔╗╔╦═╩╗╔╣╚═╦══╦═╝╠══╗  *
     * ║║║║║║╔╗║║║╔╗║║═╣╔╝ ║╚╝║║═╣║║╔╗║╔╗║╔╗║══╣  *
     * ║║║║║║╚╝║╚╣║║║║═╣║  ║║║║║═╣╚╣║║║╚╝║╚╝╠══║  *
     * ╚╝╚╝╚╩══╩═╩╝╚╩══╩╝  ╚╩╩╩══╩═╩╝╚╩══╩══╩══╝  *
     * * * * * * * * * * * * * * * * * * * * * * */


    public function getTimeStamp($report = false)
    {
        $data = explode(" ", microtime());
        if ($report)
            $mic = str_replace("0.", "", number_format($data[0], 6));
        else
            $mic = str_replace("0.", "", number_format($data[0], 4));
        return time() . $mic;
    }
}
