<?php

class Ant_Special_EmailController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();

        $this->getLayout()->getBlock("head")->setTitle($this->__("Email"));


        $postData = Mage::app()->getRequest()->getPost();
        if (isset($postData['send'])) {
            $msg = $this->sendMail($postData);
            $block = $this->getLayout()->getBlock('special_email');
            $block->setData('msg', $msg);
        }

        $this->renderLayout();
    }

    private function sendMail($postData = null) {
        $names = $postData['name'];
        $emails = $postData['email'];
        $message = trim(preg_replace('/\s\s+/', ' ', $postData['message']));
        $your_name = $postData['your_name'];
        $your_address = $postData['your_address'];

        // get admin email if customer dont input email
        if (strlen(trim($your_address)) == 0)
            $your_address = Mage::getStoreConfig('trans_email/ident_general/email');

        // set template is deafult it dont get template
        $template = $postData['template'];
        if (strlen($template) == 0)
            $template = 'default';

        // set subject is customer message
        $subject = $message;
        if (strlen($subject) == 0)
            $subject = $postData['subject'];
        if (strlen($subject) == 0)
            $subject = 'Roses Only Sinagpore';

        /* dont use template */
        /*
          $mail = Mage::getModel('core/email');
          $mail->setType('html'); // YOu can use Html or text as Mail format
          $mail->setFromEmail($your_address);
          $mail->setFromName($your_name);

          for ($i = 0; $i < sizeof($emails); $i++) {
          if (strlen($emails[$i]) > 0) {
          $mail->setToName($names[$i]);
          $mail->setToEmail($emails[$i]);
          $mail->setBody('Mail Text / Mail Content');
          $mail->setSubject('Mail Subject');
          try {
          $mail->send();
          return 'The mail has been sent successfully!';
          //$this->_redirect('');
          } catch (Exception $e) {
          return 'Unable to send.';
          //$this->_redirect('');
          }
          }
          }
         */

        /* send email use template */

        $emailTemplate = Mage::getModel('core/email_template')
                ->loadDefault('special_email_template_' . $template);
        /*
          $emailTemplateVariables = array();

          $emailTemplate->getProcessedTemplate($emailTemplateVariables);
          $emailTemplate->setSenderName($your_name);
          $emailTemplate->setSenderEmail($your_address);
          $emailTemplate->setTemplateSubject($subject);

          for ($i = 0; $i < sizeof($emails); $i++) {
          if (strlen($emails[$i]) > 0) {
          try {
          $emailTemplate->send($emails[$i], $names[$i], $emailTemplateVariables);
          print_r($emailTemplate);
          } catch (Exception $e) {
          return 'Unable to send.';
          }
          }
          }
         */


        $mail = new Zend_Mail(); //class for mail
        $mail->setBodyHtml($emailTemplate->getTemplateText()); //for sending message containing html code
        $mail->setFrom($your_address, $your_name);
        $mail->setSubject($subject);
        for ($i = 0; $i < sizeof($emails); $i++) {
            if (strlen($emails[$i]) > 0) {
                $mail->addTo($emails[$i], $names[$i]);
            }
        }

        try {
            if ($mail->send()) {
                return 'The mail has been sent successfully!';
            }
        } catch (Exception $ex) {
            return 'Error sending mail to your friend! (' . $ex . ')';
        }
    }

}