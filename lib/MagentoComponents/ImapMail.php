<?php

class MagentoComponents_ImapMail extends GeneralComponents_ImapMail
{
    /**
     * Search for a mail whose subject contains the given string
     *
     * @param $subject
     * @return bool|int
     */
    public function searchMailWithSubject($subject) {
        $storage = $this->getStorage(true); // get new storage (triggering fresh lookup for new mails)
        foreach ($storage as $idx => $message) { /* @var $message Zend_Mail_Message */
            if (strpos(iconv_mime_decode($message->subject, 0, 'UTF-8'), $subject) !== false) {
                return $idx;
            }
        }
        return false;
    }
}
