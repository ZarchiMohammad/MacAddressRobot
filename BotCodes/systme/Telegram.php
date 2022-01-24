<?php

class Telegram
{

    private static $tg;
    private static $jsonData;

    public static function getInstance($json = null)
    {
        if (self::$tg == null) {
            self::$tg = new Telegram($json);
        }
        return self::$tg;
    }

    private function __construct($json = null)
    {
        if ($json != null) {
            self::$jsonData = json_decode($json);
        }
    }

    public function getChatId()
    {
        if (isset(self::$jsonData->callback_query)) {
            return self::$jsonData->callback_query->message->chat->id;
        } else {
            if (isset(self::$jsonData->edited_message->chat->id))
                return self::$jsonData->edited_message->chat->id;
            else
                return self::$jsonData->message->chat->id;
        }
    }

    public function getFirstName()
    {
        if (isset(self::$jsonData->callback_query))
            return self::$jsonData->callback_query->from->first_name;
        elseif (isset(self::$jsonData->message->from->first_name))
            return self::$jsonData->message->from->first_name;
        else
            return null;
    }

    public function getLastName()
    {
        if (isset(self::$jsonData->callback_query))
            return self::$jsonData->callback_query->from->last_name;
        elseif (isset(self::$jsonData->message->from->last_name))
            return self::$jsonData->message->from->last_name;
        else
            return null;
    }

    public function getMessageText()
    {
        if (isset(self::$jsonData->callback_query)) {
            return self::$jsonData->callback_query->data;
        } else {
            if (isset(self::$jsonData->edited_message->text))
                return self::$jsonData->edited_message->text;
            else
                return self::$jsonData->message->text;
        }
    }

    public function getContact()
    {
        if (isset(self::$jsonData->message->contact)) {
            return true;
        } else {
            return false;
        }
    }

    public function getMessageType()
    {
        $result = null;
        if (isset(self::$jsonData->message->audio))
            $result = "audio";
        elseif (isset(self::$jsonData->message->document))
            $result = "document";
        elseif (isset(self::$jsonData->message->photo))
            $result = "photo";
        elseif (isset(self::$jsonData->message->video))
            $result = "video";
        elseif (isset(self::$jsonData->message->voice))
            $result = "voice";
        elseif (isset(self::$jsonData->message->contact))
            $result = "contact";
        elseif (isset(self::$jsonData->message->sticker))
            $result = "sticker";
        elseif (isset(self::$jsonData->message->text) || isset(self::$jsonData->callback_query->data))
            $result = "text";
        return $result;
    }

    public function isChannelPost()
    {
        $result = false;
        if (isset(self::$jsonData->channel_post))
            $result = true;
        return $result;
    }

    public function isEditChannelPost()
    {
        $result = false;
        if (isset(self::$jsonData->edited_channel_post))
            $result = self::$jsonData->edited_channel_post;
        return $result;
    }

    public function pinnedMessage()
    {
        $result = false;
        if (isset(self::$jsonData->message->pinned_message))
            $result = true;
        return $result;
    }

    public function sendMessage($chatId, $message, $replyMarkup = null)
    {
        $message = urlencode($message);
        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/sendMessage?chat_id=" . $chatId;
        $url .= "&text=" . $message;
        $url .= "&parse_mode=html";
        if ($replyMarkup == "ReplyKeyboardRemove") {
            $removeKeyboard = array('remove_keyboard' => true);
            $removeKeyboardEncoded = json_encode($removeKeyboard);
            $url .= "&reply_markup=" . $removeKeyboardEncoded;
        }
        return file_get_contents($url);
    }

    public function deleteMessage($chatId, $messageId)
    {
        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/deleteMessage?chat_id=" . $chatId;
        $url .= "&message_id=" . $messageId;
        file_get_contents($url);
    }

    public function setChatAction($chatId, $action = "typing")
    {
        /* typing for text messages
         * upload_photo for photos
         * upload_video for videos
         * record_video for video recording
         * upload_audio for audio files
         * record_audio for audio file recording
         * upload_document for general files
         * find_location for location data
         * upload_video_note for video notes
         * record_video_note for video note recording */

        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/sendChatAction?chat_id=" . $chatId;
        $url .= "&action=" . $action;
        return file_get_contents($url);
    }

    public function editMessage($chatId, $messageId, $text)
    {
        $text = urlencode($text);
        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/editMessageText?chat_id=" . $chatId;
        $url .= "&message_id=" . $messageId;
        $url .= "&text=" . $text;
        $url .= "&parse_mode=html";
        file_get_contents($url);
    }

    public function pinChatMessage($chatId, $messageId)
    {
        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/pinChatMessage?chat_id=" . $chatId;
        $url .= "&message_id=" . $messageId;
        $url .= "&disable_notification=true";
        file_get_contents($url);
    }

    public function unpinChatMessage($chatId)
    {
        $url = "https://api.telegram.org/bot" . _TOKEN;
        $url .= "/unpinAllChatMessages?chat_id=" . $chatId;
        file_get_contents($url);
    }
}
