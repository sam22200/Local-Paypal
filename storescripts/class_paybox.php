<?php
class Paybox {
    // Eléments de notre panier
    protected $PAYBOX_DOMAIN_SERVER;
    protected $PBX_SITE;
    protected $PBX_RANG;
    protected $PBX_IDENTIFIANT;
    protected $PBX_EFFECTUE;
    protected $PBX_ANNULE;
    protected $PBX_TYPEPAIEMENT;
    protected $PBX_TYPECARTE;
    protected $PBX_TOTAL;
    protected $PBX_DEVISE;
    protected $PBX_CMD;
    protected $PBX_PORTEUR;
    protected $PBX_RETOUR;
    protected $PBX_HASH;
    protected $PBX_TIME;
    protected $PBX_HMAC;
    protected $PBX_IMG;


    public function __construct($PAYBOX_DOMAIN_SERVER, $PBX_SITE, $PBX_RANG, $PBX_IDENTIFIANT, $PBX_EFFECTUE, $PBX_ANNULE, $PBX_TYPEPAIEMENT, $PBX_TYPECARTE, $PBX_TOTAL, $PBX_DEVISE, $PBX_CMD, $PBX_PORTEUR, $PBX_RETOUR, $PBX_HASH,  $PBX_TIME, $PBX_IMG){
        $this->PAYBOX_DOMAIN_SERVER = $PAYBOX_DOMAIN_SERVER;
        $this->PBX_SITE = $PBX_SITE;
        $this->PBX_RANG = $PBX_RANG;
        $this->PBX_IDENTIFIANT = $PBX_IDENTIFIANT;
        $this->PBX_EFFECTUE = $PBX_EFFECTUE;
        $this->PBX_ANNULE = $PBX_ANNULE;
        $this->PBX_TYPEPAIEMENT = $PBX_TYPEPAIEMENT;
        $this->PBX_TYPECARTE = $PBX_TYPECARTE;
        $this->PBX_TOTAL = $PBX_TOTAL;
        $this->PBX_DEVISE = $PBX_DEVISE;
        $this->PBX_CMD = $PBX_CMD;
        $this->PBX_PORTEUR = $PBX_PORTEUR;
        $this->PBX_RETOUR = $PBX_RETOUR;
        $this->PBX_HASH = $PBX_HASH;
        $this->PBX_TIME = $PBX_TIME;
        $this->PBX_HMAC = null;
        $this->PBX_IMG = $PBX_IMG;
    }

      public function computeHmac(){
        // On récupère la date au format ISO-8601
        //$dateTime = date("c");
        // On crée la chaîne à hacher sans URLencodage
        $msg =
        "PBX_SITE=$this->PBX_SITE".
        "&PBX_RANG=$this->PBX_RANG".
        "&PBX_IDENTIFIANT=$this->PBX_IDENTIFIANT".
        "&PBX_TOTAL=$this->PBX_TOTAL".
        "&PBX_DEVISE=$this->PBX_DEVISE".
        "&PBX_CMD=$this->PBX_CMD".
        "&PBX_EFFECTUE=$this->PBX_EFFECTUE".
        "&PBX_ANNULE=$this->PBX_ANNULE".
        "&PBX_TYPEPAIEMENT=$this->PBX_TYPEPAIEMENT".
        "&PBX_TYPECARTE=$this->PBX_TYPECARTE".
        "&PBX_PORTEUR=$this->PBX_PORTEUR".
        "&PBX_RETOUR=$this->PBX_RETOUR".
        "&PBX_HASH=$this->PBX_HASH".
        "&PBX_TIME=$this->PBX_TIME";
        // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on
        //renseigne dans la variable $keyTest;
        // Si la clé est en ASCII, On la transforme en binaire
        $keyTest = "0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF";
        $binKey = pack("H*", $keyTest);

        // On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et
        // la clé binaire
        // On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
        // Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne
        // suivante
        // print_r(hash_algos());
        $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
        // La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
        // On crée le formulaire à envoyer à Paybox System
        // ATTENTION : l'ordre des champs est extrêmement important, il doit
        // correspondre exactement à l'ordre des champs dans la chaîne hachée
        return $hmac;
      }

      protected function setPBX_HMAC(){
        $this->PBX_HMAC = $this->computeHmac();
      }

      public function computePbxBtn(){
        if (!isset($this->PBX_HMAC)) {
            $this->setPBX_HMAC();
        }
        $btn =
        '<form method="POST" action="'.$this->PAYBOX_DOMAIN_SERVER.'">
                          <input type="hidden" name="PBX_SITE" value="'.$this->PBX_SITE.'">
                          <input type="hidden" name="PBX_RANG" value="'.$this->PBX_RANG.'">
                          <input type="hidden" name="PBX_IDENTIFIANT" value="'.$this->PBX_IDENTIFIANT.'">
                          <input type="hidden" name="PBX_TOTAL" value="'.$this->PBX_TOTAL.'">
                          <input type="hidden" name="PBX_DEVISE" value="'.$this->PBX_DEVISE.'">
                          <input type="hidden" name="PBX_CMD" value="'.$this->PBX_CMD.'">
                          <input type="hidden" name="PBX_EFFECTUE" value="'.$this->PBX_EFFECTUE.'">
                          <input type="hidden" name="PBX_ANNULE" value="'.$this->PBX_ANNULE.'">
                          <input type="hidden" name="PBX_TYPEPAIEMENT" value="'.$this->PBX_TYPEPAIEMENT.'">
                          <input type="hidden" name="PBX_TYPECARTE" value="'.$this->PBX_TYPECARTE.'">
                          <input type="hidden" name="PBX_PORTEUR" value="'.$this->PBX_PORTEUR.'">
                          <input type="hidden" name="PBX_RETOUR" value="'.$this->PBX_RETOUR.'">
                          <input type="hidden" name="PBX_HASH" value="'.$this->PBX_HASH.'">
                          <input type="hidden" name="PBX_TIME" value="'.$this->PBX_TIME.'">
                          <input type="hidden" name="PBX_HMAC" value="'.$this->PBX_HMAC.'">
                          <input class"img img-responsive" type="image" src="'.$this->PBX_IMG.'" alt="Payer">
                        </form>';

        return $btn;
      }

      static function getErreurMsg($code_erreur) {
        switch ($code_erreur) {
            case '00000':
                $erreur_msg = __('Opération réussie.', 'openboutique_paybox_gateway');
                break;
            case '00001':
                $erreur_msg = __('La connexion au centre d\'autorisation a échoué. Vous pouvez dans ce cas là effectuer les redirections des internautes vers le FQDN', 'openboutique_paybox_gateway') . ' tpeweb1.paybox.com.';
                break;
            case '00002':
                $erreur_msg = __('Une erreur de cohérence est survenue.', 'openboutique_paybox_gateway');
                break;
            case '00003':
                $erreur_msg = __('Erreur Paybox.', 'openboutique_paybox_gateway');
                break;
            case '00004':
                $erreur_msg = __('Numéro de porteur ou crytogramme visuel invalide.', 'openboutique_paybox_gateway');
                break;
            case '00006':
                $erreur_msg = __('Accès refusé ou site/rang/identifiant incorrect.', 'openboutique_paybox_gateway');
                break;
            case '00008':
                $erreur_msg = __('Date de fin de validité incorrecte.', 'openboutique_paybox_gateway');
                break;
            case '00009':
                $erreur_msg = __('Erreur de création d\'un abonnement.', 'openboutique_paybox_gateway');
                break;
            case '00010':
                $erreur_msg = __('Devise inconnue.', 'openboutique_paybox_gateway');
                break;
            case '00011':
                $erreur_msg = __('Montant incorrect.', 'openboutique_paybox_gateway');
                break;
            case '00015':
                $erreur_msg = __('Paiement déjà effectué', 'openboutique_paybox_gateway');
                break;
            case '00016':
                $erreur_msg = __('Abonné déjà existant (inscription nouvel abonné). Valeur \'U\' de la variable PBX_RETOUR.', 'openboutique_paybox_gateway');
                break;
            case '00021':
                $erreur_msg = __('Carte non autorisée.', 'openboutique_paybox_gateway');
                break;
            case '00029':
                $erreur_msg = __('Carte non conforme. Code erreur renvoyé lors de la documentation de la variable « PBX_EMPREINTE ».', 'openboutique_paybox_gateway');
                break;
            case '00030':
                $erreur_msg = __('Temps d\'attente > 15 mn par l\'internaute/acheteur au niveau de la page de paiements.', 'openboutique_paybox_gateway');
                break;
            case '00031':
            case '00032':
                $erreur_msg = __('Réservé', 'openboutique_paybox_gateway');
                break;
            case '00033':
                $erreur_msg = __('Code pays de l\'adresse IP du navigateur de l\'acheteur non autorisé.', 'openboutique_paybox_gateway');
                break;
            // Nouveaux codes : 11/2013 (v6.1)
            case '00040':
                $erreur_msg = __('Opération sans authentification 3-DSecure, bloquée par le filtre', 'openboutique_paybox_gateway');
                break;
            case '99999':
                $erreur_msg = __('Opération en attente de validation par l\'emmetteur du moyen de paiement.', 'openboutique_paybox_gateway');
                break;
            default:
                if (substr($code_erreur, 0, 3) == '001')
                    $erreur_msg = __('Paiement refusé par le centre d\'autorisation. En cas d\'autorisation de la transaction par le centre d\'autorisation de la banque, le code erreur \'00100\' sera en fait remplacé directement par \'00000\'.', 'openboutique_paybox_gateway');
                else
                    $erreur_msg = __('Pas de message', 'openboutique_paybox_gateway');
                break;
        }
        return $erreur_msg;
    }

    /**
     * Reponse Paybox (Pour le serveur Paybox)
     *
     * @access public
     * @return void
     */
    function woocommerce_paybox_check_response()
    {
        if (isset($_GET['order']) && isset($_GET['sign']))
        { // On a bien un retour ave une commande et une signature
            $order = new WC_Order((int) $_GET['order']); // On récupère la commande
            $pos_qs = strpos($_SERVER['REQUEST_URI'], '?');
            $pos_sign = strpos($_SERVER['REQUEST_URI'], '&sign=');
            $return_url = substr($_SERVER['REQUEST_URI'], 1, $pos_qs - 1);
            $data = substr($_SERVER['REQUEST_URI'], $pos_qs + 1, $pos_sign - $pos_qs - 1);
            $sign = substr($_SERVER['REQUEST_URI'], $pos_sign + 6);
            // Est-on en réception d'un retour PayBox
            $my_WC_Paybox = new WC_Paybox();
            if (str_replace('//', '/', '/' . $return_url) == str_replace('//', '/', $my_WC_Paybox->return_url))
            {
                $std_msg = __('Paybox Return IP', 'openboutique_paybox_gateway').' : '.WC_Paybox::getRealIpAddr().'<br/>'.$data.'<br/><div style="word-wrap:break-word;">'.__('PBX Sign', 'openboutique_paybox_gateway').' : '. $sign . '<div>';
                @ob_clean();
                // Traitement du retour PayBox
                // PBX_RETOUR=order:R;erreur:E;carte:C;numauto:A;numtrans:S;numabo:B;montantbanque:M;sign:K
                if (isset($_GET['erreur']))
                {
                    switch ($_GET['erreur'])
                    {
                        case '00000':
                            // OK Pas de pb
                            // On vérifie la clef
                            // recuperation de la cle publique
                            $fp = $filedata = $key = FALSE;
                            $fsize = filesize(dirname(__FILE__) . '/lib/pubkey.pem');
                            $fp = fopen(dirname(__FILE__) . '/lib/pubkey.pem', 'r');
                            $filedata = fread($fp, $fsize);
                            fclose($fp);
                            $key = openssl_pkey_get_public($filedata);
                            $decoded_sign = base64_decode(urldecode($sign));
                            $verif_sign = openssl_verify($data, $decoded_sign, $key);
                            if ($verif_sign == 1) 
                            {   // La commande est bien signé par PayBox
                                // Si montant ok
                                if ((int) (100 * $order->get_total()) == (int) $_GET['montantbanque']) 
                                {
                                    $order->add_order_note('<p style="color:green"><b>'.__('Paybox Return OK', 'openboutique_paybox_gateway').'</b></p><br/>' . $std_msg);
                                    $order->payment_complete();
                                    wp_die(__('OK', 'openboutique_paybox_gateway'), '', array('response' => 200));
                                }
                                $order->add_order_note('<p style="color:red"><b>'.__('ERROR', 'openboutique_paybox_gateway').'</b></p> '.__('Order Amount', 'openboutique_paybox_gateway').'.<br/>' . $std_msg);
                                wp_die(__('KO Amount modified', 'openboutique_paybox_gateway').' : ' . $_GET['montantbanque'] . ' / ' . (100 * $order->get_total()), '', array('response' => 406));
                            }
                            $order->add_order_note('<p style="color:red"><b>'.__('ERROR', 'openboutique_paybox_gateway').'</b></p> '.__('Signature Rejected', 'openboutique_paybox_gateway').'.<br/>' . $std_msg);
                            wp_die(__('KO Signature', 'openboutique_paybox_gateway'), '', array('response' => 406));
                        default:
                            $order->add_order_note('<p style="color:red"><b>'.__('PBX ERROR', 'openboutique_paybox_gateway').' ' . $_GET['erreur'] . '</b> ' . WC_Paybox::getErreurMsg($_GET['erreur']) . '</p><br/>' . $std_msg);
                            wp_die(__('OK received', 'openboutique_paybox_gateway'), '', array('response' => 200));
                    }
                } else
                    wp_die(__('Test AutoResponse OK', 'openboutique_paybox_gateway'), '', array('response' => 200));
            }
        }
    }
}
?>