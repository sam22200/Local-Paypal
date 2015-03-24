<?php

  require '/PHPMailer-master/PHPMailerAutoload.php';
  class Mail {

    private $__mailer;
    private $sender;
    private $host;
    private $receiver;
    private $password;
    private $port;
    private $body;
    private $title;
    private $secure;


    public function __construct(){}

    public function getSender(){
      return $this->sender;
    }

    public function setSender($sender){
      $this->sender = $sender;
    }

    public function getHost(){
      return $this->host;
    }

    public function setHost($host){
      $this->host = $host;
    }

    public function getReceiver(){
      return $this->receiver;
    }

    public function setReceiver($receiver){
      $this->receiver = $receiver;
    }

    public function getPassword(){
      return $this->password;
    }

    public function setPassword($password){
      $this->password = $password;
    }

    public function getPort(){
      return $this->port;
    }

    public function setPort($port){
      $this->port = $port;
    }

    public function getBody(){
      return $this->body;
    }

    public function setBody($body){
      $this->body = $body;
    }

    public function getTitle(){
      return $this->title;
    }

    public function setTitle($title){
      $this->title = $title;
    }

    public function getSecure(){
      return $this->secure;
    }

    public function setSecure($secure){
      $this->secure = $secure;
    }

    public function init(){
      $this->__mailer = new PHPMailer();
      $this->__mailer->STMPDebug = 1;
      $this->__mailer->STMPAuth = true;
      $this->__mailer->Host = $this->getHost();
      $this->__mailer->SMTPSecure = $this->getSecure();
      $this->__mailer->Username = $this->getSender();
      $this->__mailer->Password = $this->getPassword();
      $this->__mailer->Port = $this->getPort();
      $this->__mailer->IsSMTP();
      $this->__mailer->AddAddress($this->getReceiver());
    }

    public function sendMail(){
      $this->__mailer->Subject = $this->title;
      $this->__mailer->body = $this->body;
      $this->__mailer->WordWrap = 200;
      $this->__mailer->Send();
    }
}
?>