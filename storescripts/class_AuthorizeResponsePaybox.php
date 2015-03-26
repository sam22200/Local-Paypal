<?php

class AuthorizeResponsePaybox {

  private $auto;
  private $montant;
  private $type;
  private $date;
  private $day;
  private $ref;
  private $id;
  private $erreur;
  private $bin6;
  private $signature;
  private $list;
  private $username;
  private $order;

  //Construit l'objet AuthorizeResponsePaybox
  public function __construct($data) {
      //Affectation de ses attributs
      parse_str($data, $output);

      if (empty($output['signature'])){
        throw new Exception("Signature non présente.");
      }
      $this->signature = $output['signature'];

      //Verification de la signature (etape nécessaire)
      if (!$this->verifySignature()) {
       throw new Exception('Mauvaise Signature de la réponse.');
      }

      if (empty($output['auto'])){
        throw new Exception("Code auto non transmis.");
      }
      $this->auto = $output['auto'];

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

      if (empty($output['day'])){
        throw new Exception("Code date non transmis.");
      }
      $this->day = $output['day'];

      if (empty($output['ref'])){
        throw new Exception("Code ref non transmis.");
      }
      $arrStr = explode( '|' , $output['ref']);
      $this->order = $arrStr[3];
      $this->ref = $arrStr[2];
      $this->list = $arrStr[1];
      $this->username = $arrStr[0];

      if (empty($output['id'])){
        throw new Exception("Code id non transmis.");
      }
      $this->id = $output['id'];

      if (empty($output['bin6'])){
        throw new Exception("Code bin6 non transmis.");
      }
      $this->bin6 = $output['bin6'];
  }

  //Retourne l'id de la transaction PayBox
  public function getTransactionReference()
  {
    return isset($this->id) ? $this->id : null;
  }

  //Retourne vrai si le code erreur est "00000"
  public function isSuccessful() {
    return isset($this->erreur) && '00000' === $this->erreur;
  }

  //Retourne message d'eereur si la transaction est invalide
  public function getMessage()
  {
    return !$this->isSuccessful() ? 'Transaction Erronnée' : null;
  }

  //Chemin de la clé publique
  protected function getPublicKey()
  {
    return  __DIR__ . '/../config/pubkey.pem';
    //return './config/pubkey.pem';
  }

  //Vérifie la signature
  protected function verifySignature() {
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

  //Retourne le montant de la transaction
  public function getAmount()
  {
    return isset($this->montant) ? $this->montant : null;
  }

  //Retourne le montant de la transaction
  public function getUsername()
  {
    return isset($this->username) ? $this->username : null;
  }

  //Retourne le montant de la transaction
  public function getRef()
  {
    return isset($this->ref) ? $this->ref : null;
  }

  //Retourne le montant de la transaction
  public function getList()
  {
    return isset($this->list) ? $this->list : null;
  }

  //Retourne le montant de la transaction
  public function getOrder()
  {
    return isset($this->order) ? $this->order : null;
  }

  //Retourne la date de la transaction
  public function getDate()
  {
    return isset($this->date) ? $this->date : null;
  }

  //Retourne la date de la transaction
  public function getType()
  {
    return isset($this->type) ? $this->type : null;
  }

  //Retourne la date de la transaction
  public function getDay()
  {
    return isset($this->day) ? $this->day : null;
  }

  //Retourne la date de la transaction
  public function getBin6()
  {
    return isset($this->bin6) ? $this->bin6 : null;
  }


  //Verifie la cohérence du montant en fonction de la liste de produit
  protected function checkAmount(){
      // Connect to the MySQL database
      require_once "class_connexion.php";
      $connection = new createConnection();
      $connection->connectToDatabase();
      $connection->selectDatabase();

      //Scinde la string des produits en Code et Qte
      $str = $this->list;
      $arrP = explode(',',$str);
      $i = 0;
      foreach($arrP as $p){
        $arr_tmp = explode('-',$p);
        $arr2[$i] = $arr_tmp;
        $i = $i+1;
      }
      //Calcule la somme
      $sommePbx = $this->getAmount()/100;
      //Compare la somme avec le montant
      $i = 0;
      $sommeTh = 0;
      foreach($arr2 as $pr){
        $sql = mysql_query("SELECT price FROM products WHERE id='$pr[0]' LIMIT 1");
        while($row = mysql_fetch_array($sql)){
          $p_price = $row["price"];
        }
        $sommeTh += $p_price*$pr[1];
        $i = $i+1;
      }
      //Décision
      if ($sommePbx == $sommeTh){
        return true;
      } else {
        return false;
      }
  }

  //Check si le membre 'Auto' est alpha-numérique
  public function checkAuto(){
    return ctype_alnum($this->auto);
  }

  public function computeChecks(){
    $res = false;
    if ($this->isSuccessful()){
      if ($this->checkAmount()){
        if ($this->checkAuto()){
          $res = true;
        } else {
          $res = false;
          throw new Exception("Auto n'est pas Alphanumérique !");
        }
      } else {
        $res = false;
        throw new Exception("Le montant n'est pas cohérent !");
      }
    } else {
      $res = false;
      throw new Exception("Le code de retour n'est pas bon !");
    }
    return $res;
  }

  //Stocke en base les informations
  public function storeTransac(){
    if (!$this->computeChecks()){
      return false;
    }
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    //Insert les valeurs en BASE
    $price = $this->montant/100;
    $query="INSERT INTO transactions (product_id_array, mc_gross, txn_id, payment_date, day, payment_type, payment_status, invoiceNumber, address_zip) values('$this->list', '$price', '$this->order', '$this->date','$this->day', '$this->type', 'OK', '$this->ref', '$this->bin6')";
    mysql_query($query)  or die(mysql_error());

    return true;
  }


}
?>
