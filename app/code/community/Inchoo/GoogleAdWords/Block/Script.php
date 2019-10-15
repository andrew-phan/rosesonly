<?php
/**
 * INCHOO's FREE EXTENSION DISCLAIMER
 *
 * Please do not edit or add to this file if you wish to upgrade Magento
 * or this extension to newer versions in the future.
 *
 * Inchoo developers (Inchooer's) give their best to conform to
 * "non-obtrusive, best Magento practices" style of coding.
 * However, Inchoo does not guarantee functional accuracy of specific
 * extension behavior. Additionally we take no responsibility for any
 * possible issue(s) resulting from extension usage.
 *
 * We reserve the full right not to provide any kind of support for our free extensions.
 *
 * You are encouraged to report a bug, if you spot any,
 * via sending an email to bugreport@inchoo.net. However we do not guaranty
 * fix will be released in any reasonable time, if ever,
 * or that it will actually fix any issue resulting from it.
 *
 * Thank you for your understanding.
 */

/**
 * @category Inchoo
 * @package Inchoo_GoogleAdWords
 * @author Domagoj Potkoc <domagoj.potkoc@inchoo.net>
 * @copyright Inchoo <http://inchoo.net>
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_GoogleAdWords_Block_Script extends Mage_Core_Block_Text
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setGoogleConversionId(Mage::getStoreConfig('google/inchoo_google_adwords/google_conversion_id'));
        $this->setGoogleConversionLanguage(Mage::getStoreConfig('google/inchoo_google_adwords/google_conversion_language'));
        $this->setGoogleConversionFormat(Mage::getStoreConfig('google/inchoo_google_adwords/google_conversion_format'));
        $this->setGoogleConversionColor(Mage::getStoreConfig('google/inchoo_google_adwords/google_conversion_color'));
        $this->setGoogleConversionLabel(Mage::getStoreConfig('google/inchoo_google_adwords/google_conversion_label'));
    }

    protected function _toHtml()
    {
        $html = "";

        if(Mage::helper('inchoo_google_adwords')->isTrackingAllowed()){

            $this->setAmount(Mage::helper('inchoo_google_adwords')->getOrderTotal());
            $html .= '
   	<!-- Google Code for Purchase Conversion Page -->
	<script type="text/javascript">
	/* <![CDATA[ */
	var google_conversion_id = '.$this->getGoogleConversionId().';
	var google_conversion_language = "'.$this->getGoogleConversionLanguage().'";
	var google_conversion_format = "'.$this->getGoogleConversionFormat().'";
	var google_conversion_color = "'.$this->getGoogleConversionColor().'";
	var google_conversion_label = "'.$this->getGoogleConversionLabel().'";
	var google_conversion_value = 0;
	if ('.$this->getAmount().') {
  	google_conversion_value = '.$this->getAmount().';
	}
	/* ]]> */
	</script>
	<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
	</script>
	<noscript>
	<div style="display:inline;">
	<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/'.$this->getGoogleConversionId().'/?value='.$this->getAmount().'&amp;label='.$this->getGoogleConversionLabel().'&amp;guid=ON&amp;script=0"/>
	</div>
	</noscript>';
        }

        return $html;
    }
}
