<?php


class MagentoComponents_ImapMail extends GeneralComponents_ImapMail
{
    /**
     * Wait for newsletter sign up mail
     *
     * @param array $userAccount
     */
    public function checkNewsletterSignUpMail($userAccount) {

        /* Check for mail */
        $registrationMailSubjectTemplate = $this->__('Newsletter subscription success');

        // replace markers with information from $userAccount
        $subject = $registrationMailSubjectTemplate;
        foreach ($userAccount as $key => $value) {
            $subject = str_replace('###'.strtoupper($key).'###', $value, $subject);
        }

        $idx = $this->waitForMailWhoseSubjectContains($subject);
        $message = $this->getStorage()->getMessage($idx);

        $content = Zend_Mime_Decode::decodeQuotedPrintable($message->getContent());

        $this->getStorage()->removeMessage($idx);
    }

    /**
     * Wait for newsletter sign out mail
     *
     * @param array $userAccount
     */
    public function checkNewsletterSignOutMail($userAccount) {

        /* Check for mail */
        $registrationMailSubjectTemplate = $this->__('Newsletter unsubscription success');

        // replace markers with information from $userAccount
        $subject = $registrationMailSubjectTemplate;
        foreach ($userAccount as $key => $value) {
            $subject = str_replace('###'.strtoupper($key).'###', $value, $subject);
        }

        $idx = $this->waitForMailWhoseSubjectContains($subject);
        $message = $this->getStorage()->getMessage($idx);

        $content = Zend_Mime_Decode::decodeQuotedPrintable($message->getContent());

        $this->getStorage()->removeMessage($idx);

        $this->getTest()->assertContains('unsubscription success', $content);
    }
}

