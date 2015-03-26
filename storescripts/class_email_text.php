<?php
date_default_timezone_set('Europe/Paris');
  class EmailText {
    const STORE_NAME = "PRAXEDO STORE";
    const EMAIL_SEPARATOR = '------------------------------------------------------';
    const EMAIL_TEXT_SUBJECT = 'Traitement de la commande';
    const EMAIL_TEXT_ORDER_NUMBER = 'Numéro de commande :';
    const EMAIL_TEXT_INVOICE_URL = 'Numéro de facture :';
    const EMAIL_TEXT_DATE_ORDERED = 'Date de commande :';
    const EMAIL_TEXT_PRODUCTS = 'Produits';
    const EMAIL_TEXT_SUBTOTAL = 'Sous-total :';
    const EMAIL_TEXT_TAX = 'TVA :        ';
    const EMAIL_TEXT_SHIPPING = 'Expédition : ';
    const EMAIL_TEXT_TOTAL = 'Total :    ';
    const EMAIL_TEXT_DELIVERY_ADDRESS = 'Adresse de livraison';
    const EMAIL_TEXT_METHOD_PAYMENT = 'Méthode de Paiement : ';
    const EMAIL_TEXT_CARD_TYPE = 'Type de Carte : ';
    const EMAIL_TEXT_BILLING_ADDRESS = 'Adresse de facturation';
    const EMAIL_TEXT_PAYMENT_METHOD = 'Mode de paiement';
    const TEXT_EMAIL_VIA = 'par l\'intermédiaire de';

    private $body;
    private $subject;
    private $invoice; //"ADAZDAAC4648D6"
    private $order; // "PXO619668"
    private $date; //"24/03/2015"
    private $day;
    private $username;  //"sam22200"
    private $total;  //"300"
    private $paymentMethod; //"VISA"
    private $typePayment; //"CARTE"
    private $products_ordered; //"413"=>"3","414"=>"1"
    private $devise; //"EUR"
    private $bin6; //"418678"

    function __construct($inv, $order, $date, $day, $username, $total, $paymentMethod, $typePayment, $products_ordered, $devise, $bin6)
    {
      $this->invoice = $inv;
      $this->order = $order;
      $this->date = $date;
      $this->day = substr($day,0,2)."/".substr($day,2,2)."/".substr($day,4,4);
      $this->username = $username;
      $this->total = $total;
      $this->paymentMethod = $paymentMethod;
      $this->typePayment = $typePayment;
      $this->products_ordered = $this->computeArrayProducts($products_ordered);
      $this->devise = $devise;
      $bin6 .= "**********";
      $this->bin6 = $bin6;

    }

    private function computeArrayProducts($str){
        $arrP = explode(',',$str);
        $i = 0;
        //Pour chaque Str, scinde en deux avec le '-' pour id et qte
        foreach($arrP as $p){
          $arr_tmp = explode('-',$p);
          $arr2[$i] = $arr_tmp;
          $i = $i+1;
        }
        return $arr2;
    }

    public function computeBody(){
      //setlocale(LC_TIME, "fr_FR");
      $body = "";
      $body = "";
      $body .= EmailText::STORE_NAME . "\n" .
      EmailText::EMAIL_SEPARATOR . "\n\n" .
      EmailText::EMAIL_TEXT_ORDER_NUMBER . ' ' . "\n" . '' . $this->order . "\n\n" .
      EmailText::EMAIL_TEXT_INVOICE_URL . ' ' . "\n" . '' . $this->invoice . "\n\n" .
      EmailText::EMAIL_TEXT_DATE_ORDERED . ' ' . "\n" . $this->day . ' à ' . $this->date . "\n\n\n";
      $body .= EmailText::EMAIL_TEXT_PRODUCTS . "\n" .
      EmailText::EMAIL_SEPARATOR . "\n\n";

      for ($i = 0, $n = sizeof($this->products_ordered); $i < $n; $i++) {
        $body .= "Référence Article : " . $this->products_ordered[$i][0] . '   -    Quantité : X ' . $this->products_ordered[$i][1] . "\n";
        //$body .= "Reference Article : " . $this->products_ordered[$i][0] . "\n";
      }

      $body .= EmailText::EMAIL_SEPARATOR . "\n".
      "TOTAL : ". $this->total . ' ' . $this->devise . ' ' . "\n\n";

      $body .= EmailText::EMAIL_TEXT_PAYMENT_METHOD . "\n" .
      EmailText::EMAIL_SEPARATOR . "\n\n";
      $body .= EmailText::EMAIL_TEXT_METHOD_PAYMENT . ' ' . $this->paymentMethod . "\n" .
      EmailText::EMAIL_TEXT_CARD_TYPE . ' ' . $this->typePayment . "\n";
      $body .= "Carte utilisee : " . ' ' . $this->bin6 . "\n\n";

/*      if ($payment_class->email_footer) {
      $this->body .= $payment_class->email_footer . "\n\n";
      $this->invoice.;*/

      $this->setBody($body);
    }

    public function computeSubject(){
      $subject = "";
      $subject .= EmailText::EMAIL_TEXT_SUBJECT;
      $this->setSubject($subject);
    }

    private function setBody($str){
      $this->body = $str;
    }

    public function getBody(){
      return $this->body;
    }

    private function setSubject($str){
      $this->subject = $str;
    }

    public function getSubject(){
      return $this->subject;
    }
  }
?>