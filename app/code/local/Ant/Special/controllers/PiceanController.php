<?php

class Ant_Special_PiceanController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Picean"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("valentine", array(
            "label" => $this->__("Picean"),
            "title" => $this->__("Picean")
        ));

        $this->renderLayout();
    }
    
    public function EdmAction(){
        $this->loadLayout(); 
        $this->renderLayout();
    }
}