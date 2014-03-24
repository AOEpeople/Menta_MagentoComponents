<?php

/**
 * Components for emails
 */
class MagentoComponents_ImapMail extends GeneralComponents_ImapMail
{
    /**
     * Wait for newsletter sign up mail
     *
     * @param array $userAccount
     */
    public function checkNewsletterSignUpMail($userAccount)
    {
        /* Check for mail */
        $newsletterSubscribeTemplateSubject = $this->__('Newsletter subscription success');

        // replace markers with information from $userAccount
        $subject = $newsletterSubscribeTemplateSubject;
        foreach ($userAccount as $key => $value) {
            $subject = str_replace('###' . strtoupper($key) . '###', $value, $subject);
        }

        $idx = $this->waitForMailWhoseSubjectContains($subject);
        $message = $this->getStorage()->getMessage($idx);

        $content = Zend_Mime_Decode::decodeQuotedPrintable($message->getContent());

        $this->getStorage()->removeMessage($idx);

        $this->getTest()->assertContains('subscription success', $content);
    }

    /**
     * Wait for newsletter sign out mail
     *
     * @param array $userAccount
     */
    public function checkNewsletterSignOutMail($userAccount)
    {
        /* Check for mail */
        $newsletterUnsubscribeTemplateSubject = $this->__('Newsletter unsubscription success');

        // replace markers with information from $userAccount
        $subject = $newsletterUnsubscribeTemplateSubject;
        foreach ($userAccount as $key => $value) {
            $subject = str_replace('###' . strtoupper($key) . '###', $value, $subject);
        }

        $idx = $this->waitForMailWhoseSubjectContains($subject);
        $message = $this->getStorage()->getMessage($idx);

        $content = Zend_Mime_Decode::decodeQuotedPrintable($message->getContent());

        $this->getStorage()->removeMessage($idx);

        $this->getTest()->assertContains('unsubscription success', $content);
    }

    /**
     * Wait for reset password mail
     *
     * @param $userAccount
     * @return string
     */
    public function checkResetPasswordMail($userAccount)
    {
        $resetPasswordTemplateSubject = 'Password Reset Confirmation for ###FIRSTNAME### ###LASTNAME###';

        // replace markers with information from $userAccount
        $subject = $resetPasswordTemplateSubject;
        foreach ($userAccount as $key => $value) {
            $subject = str_replace('###' . strtoupper($key) . '###', $value, $subject);
        }

        $idx = $this->waitForMailWhoseSubjectContains($subject);
        // $uid = $mail->getStorage()->getUniqueId($idx);
        $message = $this->getStorage()->getMessage($idx);

        // $content = Zend_Mime_Decode::decodeQuotedPrintable($message->getContent());
        $content = quoted_printable_decode($message->getContent());

        // cleanup: remove mail
        $this->getStorage()->removeMessage($idx);

        $this->getTest()->assertContains('There was recently a request to change the password for your account.', $content);

        return $content;
        // reset link
    }

    /**
     * Get reset password link from mail
     *
     * @param $mailContent
     * @return array
     */
    public function getResetPasswordLink($mailContent)
    {
        $resetLink = $this->findResetPasswordLink($mailContent);

        $this->getTest()->assertNotEmpty($resetLink, 'No reset link found.');

        return $resetLink;
    }

    /**
     * Extract links from given string
     *
     * @param $content
     * @return array
     */
    protected function findResetPasswordLink($content)
    {
        $links = array();
        preg_match_all('/<a.*href="(.+?resetpassword.+?)".*>/', $content, $links);
        return $links[1][0];
    }
}

