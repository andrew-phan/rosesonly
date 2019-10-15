<?php
class Mage_Contacts_IndexController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    public function preDispatch()
    {
        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('contactForm')
            ->setFormAction( Mage::getUrl('*/*/post') );

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function _postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }

   public function postAction(){
        $post = $this->getRequest()->getPost();
        if(isset($post['type']) && $post['type'] == 'feedback'){
            return $this->feedbackAction();
        }else{
            $mail = Mage::getModel('core/email');
            $mail->setToName('Roses Only Singapore');        
            
            $mail->setToEmail(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));        
            $mail->setSubject('New Comment from '.$post['name'].'<'.$post['email'].'>');
            $mail->setFromEmail($post['email']);
            $mail->setFromName($post['name']);
            $mail->setType('html');// YOu can use Html or text as Mail format
            $html  = 'Salutation : '.$post['salutation'].'<br/>';
            $html .= 'Name : '.$post['name'].'<br/>';
            $html .= 'Country : '.$post['country'].'<br/>';
            $html .= 'State : '.$post['state'].'<br/>';
            $html .= 'Address : '.$post['add1'].'<br/>';
            $html .= ' '.$post['add2'].'<br/>';
            $html .= ' '.$post['add3'].'<br/>';
            $html .= 'City : '.$post['city'].'<br/>';
            $html .= 'Zip Postal Code : '.$post['postal'].'<br/>';
            $html .= 'Telephone : '.$post['telephone'].'<br/>';
            $html .= 'Fax : '.$post['fax'].'<br/>';
            $html .= 'Request : ';
            
            if ($post['general_question']=='on')    $html .= 'General Question; ';
            if ($post['contact_us'] =='on')         $html .= 'Contact Us; ';
            if ($post['site_issues']=='on')    $html .= 'Site Issue; ';
            if ($post['modify_order'] =='on')         $html .= 'Modify Order; ';
            if ($post['customer_service']=='on')    $html .= 'Customer Service; ';
            if ($post['others'] =='on')         $html .= 'Others; ';
            
            $html .= '<br/>';
            $html .= 'Comment : '.$post['comment'].'<br/>';
            
            $mail->setBody($html);

            try {
                $mail->send();
                $mail->setSubject('New Comment from '.$post['name'].'<'.$post['email'].'> - '. Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));               
                $mail->setToEmail('hoang.dinh21@gmail.com');
                $mail->send();
                Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('Unable to send.');
                $this->_redirect('*/*/');
                return;
            }
        }
    }
    
    public function feedbackAction(){
        $post = $this->getRequest()->getPost();
        
        $mail = Mage::getModel('core/email');
        $mail->setToName('Roses Only Singapore');        
        
        $mail->setToEmail(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));        
        $mail->setSubject('New Feedback from '.$post['name'].'<'.$post['email'].'>');
        $mail->setFromEmail($post['email']);
        $mail->setFromName($post['name']);
        $mail->setType('html');// YOu can use Html or text as Mail format
        $html  = sprintf ('<style>td{border:solid 1px #333;}</style>        
        <table class="form">
            <tr>
                <td style="width:400px; padding-right: 20px;"><label>Name</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Email</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Address</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Flower Quality</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Packaging</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Flower Presentation</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Delivery</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Did you follow the care instructions?</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Have you ever made a purchase at rosesonly.com.sg?</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Would you recommend Roses Only to friends and colleagues?</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Would you like to receive information about our gifting options</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Would you like to receive our Roses Only e-newsletter highlighting monthly specials, competitions and promotions?</label><span class="required">*</span></td>
                <td>%s</td>
            </tr>
            <tr>
                <td><label>Other Comments</label></td>
                <td>%s</td>
            </tr>
        </table>',
        $post['name'],
        $post['email'],
        $post['address'], 
        $post['quality'],
        $post['packaging'],
        $post['presentation'],
        $post['delivery'], 
        $post['care'],
        $post['purchase'],
        $post['friends'],
        $post['gift'],
        $post['e-newsletter'], 
        $post['comment']
        );
        $mail->setBody($html);
        if($post['e-newsletter']=='Yes')
               $status = Mage::getModel('newsletter/subscriber')->subscribe($post['email']);
        try {
            $mail->send();
            $mail->setSubject('New Feedback from '.$post['name'].'<'.$post['email'].'> - '. Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));
            $mail->setToEmail('hoang.dinh21@gmail.com');
            $mail->send();
            Mage::getSingleton('core/session')->addSuccess('We have received your feedback. Thank you.');
            $this->_redirect('../../feedback');
            return;
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable to send.');
            $this->_redirect('*/*/');
            return;
        }
    }
}