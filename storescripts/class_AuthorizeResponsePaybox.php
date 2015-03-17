<?php

class AuthorizeResponsePaybox {

  private $montant;
  private $type;
  private $date;
  private $ref;
  private $id;
  private $erreur;
  private $bin6;
  private $signature;

  public function __construct($data) {
        parse_str($data, $output);

        if (empty($output['signature'])){
          throw new Exception("Signature non présente.");
        }
        $this->signature = $output['signature'];

        if (!$this->verifySignature()) {
         throw new Exception('Mauvaise Signature de la réponse.');
        }

        if (empty($output['erreur'])){
          throw new Exception("Code erreur non transmis.");
        }
        $this->erreur = $output['erreur'];

        if (empty($output['montant'])){
          throw new Exception("Code montant non transmis.");
        }
        $this->montant = $output['montant'];

        if (empty($output['type'])){
          throw new Exception("Code type non transmis.");
        }
        $this->type = $output['type'];

        if (empty($output['date'])){
          throw new Exception("Code date non transmis.");
        }
        $this->date = $output['date'];

        if (empty($output['ref'])){
          throw new Exception("Code ref non transmis.");
        }
        $this->ref = $output['ref'];

        if (empty($output['id'])){
          throw new Exception("Code id non transmis.");
        }
        $this->id = $output['id'];

        if (empty($output['bin6'])){
          throw new Exception("Code bin6 non transmis.");
        }
        $this->bin6 = $output['bin6'];
  }

  public function getTransactionReference()
  {
      return isset($this->id) ? $this->id : null;
  }

  public function isSuccessful() {
    return isset($this->erreur) && '00000' === $this->erreur;
  }

  public function getMessage()
  {
    return !$this->isSuccessful() ? 'Transaction Erronnée' : null;
  }

  protected function getPublicKey()
  {
    return  __DIR__ . '/../config/pubkey.pem';
  }

public function verifySignature() {
    //Récupere les parametres
    $url = parse_url($_SERVER['REQUEST_URI']);
    $vars = $url['query'];
    // enlever la signature des datas
    $vars = preg_replace(',&signature=.*$,','',$vars);

    //encode la signature
    $sign = $_REQUEST['signature'];
    $sign = base64_decode($sign);

    //$this->initSignature();
    $file = fopen($this->getPublicKey(), 'r');
    $cert = fread($file, 1024);
    fclose($file);
    $publicKey = openssl_pkey_get_public($cert);

    $result = openssl_verify(
      $vars,
      $sign,
      $publicKey,
      'sha1WithRSAEncryption'
    );
    openssl_free_key($publicKey);

    if ($result == 1) {
      return true;
    } elseif ($result == 0) {
      throw new Exception('Signature non valide.');
    } else {
      throw new Exception('Erreur durant la vérification de la signature.');
    }

    return false;
}

  public function getAmount()
  {
    return $this->montant;
  }

}
?>