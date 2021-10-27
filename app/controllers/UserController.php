<?php 

class UserController extends Controller{

    public function registeruserAction()
	{
		// Initialisation des variables 
		$user = new User();
		$error = false;		

		// Gestion de l'update
		$id = $this->_getParam('id');

		if($id != null){

			// On remplit le formulaire
			$userUpdate = $user->fetchOne($id);
			$this->view->userUpdate = $userUpdate;			
		}

		// Gestion de l'envoi du formulaire
		if($this->_request->isPost()){
			// On vérifie le nom
			if( $this->_getParam('nom') != ''){
				$nom = $this->_getParam('nom');
			}
			else{
				Utils::errmsg('Merci de renseigner un nom.');
				$error = true;
			}
			// On vérifie le prenom
			if( $this->_getParam('prenom') != ''){
				$prenom = $this->_getParam('prenom');
			}
			else{
				Utils::errmsg('Merci de renseigner un prenom');
				$error = true;
			}
			// On vérifie le pseudo
			if( $this->_getParam('pseudo') != ''){
				if( $id == null && $user->fetchPseudo($this->_getParam('pseudo'))){
					
					Utils::errmsg('Ce pseudo existe déjà, merci d\'en renseigner un autre.');
					$pseudo = '';
					$error = true;
				}
				else{
					$pseudo = $this->_getParam('pseudo');
				}				
			}
			else{
				Utils::errmsg('Merci de renseigner un pseudo');
				$error = true;
			}
			// On vérifie le mot de passe
			if( $id == null && $this->_getParam('pass') != ''){
				if ( $this->_getParam('confpass') == null ){
					Utils::errmsg('Merci de confirmer le mot de passe');
					$error = true;
					$pass = '';
				}
				else{
					if ( $this->_getParam('pass') == $this->_getParam('confpass')){
						$pass = md5($this->_getParam('pass'));
					}
					else{
						Utils::errmsg('La confirmation du mot de passe ne correspond pas au mot de passe.');
						$error = true;
						$pass = '';
					}
				}				
			}
			elseif ($id == null) {
				Utils::errmsg('Merci de renseigner un mot de passe');
				$error = true;
			}elseif( $id != null && $this->_getParam('passUpdate') != ''){

				if ( $this->_getParam('confpassUpdate') == null ){
					Utils::errmsg('Merci de confirmer le mot de passe');
					$error = true;
					$pass = '';
				}
				else{
					if ( $this->_getParam('passUpdate') == $this->_getParam('confpassUpdate')){
						$pass = md5($this->_getParam('passUpdate'));
					}
					else{
						Utils::errmsg('La confirmation du mot de passe ne correspond pas au mot de passe.');
						$error = true;
						$pass = '';
					}
				}

			}
			// On vérifie l'adresse de facturation
			if( $this->_getParam('adrsFacturation') !=''){
				$adrsFacturation = $this->_getParam('adrsFacturation');
			}
			else{
				Utils::errmsg('Merci de renseigner une adresse de facturation');
				$error = true;
			}
			// On vérifie l'adresse de livraison
			
			if( $this->_getParam('adrsLivraison') == ''){
				$adrsLivraison = $this->_getParam('adrsFacturation');
			}
			else{
				$adrsLivraison = $this->_getParam('adrsLivraison');
			}
			// On vérifie le mail
			if($this->_getParam('mail') != ''){
				if(filter_var($this->_getParam('mail'), FILTER_VALIDATE_EMAIL)){
					$mail = $this->_getParam('mail');
				}
				else{
					Utils::errmsg('Merci de renseigner un mail valide');
					$mail = '';
					$error = true;
				}
			}
			else{
				Utils::errmsg('Merci de renseigner une adresse mail');
				$mail = '';
				$error = true;
			}
			            
            $role = 0;

			// Traitement image 
			if ($_FILES['image']['name'] != ''){
				if($id != null){
					// On supprime le fichier existant
					unlink($this->_getParam('imageUpdate'));
				}
				// On enregistre le nouveau fichier
				$titleImg = md5(uniqid(rand(),true));				
				$extensions = ['jpg','jpeg','gif','png'];
				$extension_upload = strtolower(substr(strrchr($_FILES['image']['name'],'.'),1));
				$titleImg = $titleImg.'.'.$extension_upload;
				Utils::upload('image','../web/avatar/'.$titleImg,FALSE,$extensions);
				$img = 'avatar/'.$titleImg;
			}
			else{
				if($id != null){
					$img = $this->_getParam('imageUpdate');
				}
				else{
					$img = '';
				}								
			}

			// Configuration des paramètres
			$params = [
				'image' => $img,
				'nom' => $nom,
				'prenom' => $prenom,
				'pseudo' => $pseudo,
				'adrsFacturation' => $adrsFacturation,
				'adrsLivraison' => $adrsLivraison,
				'mail' => $mail,
				'role' => $role
			];
			
			if ($id != null){
				$params ['id'] = $id;

				if ( $this->_getParam('passUpdate') != ''){
					$params ['pass'] = $pass;
				}
			}
			else{
				$params ['pass'] = $pass;
			}
			
			// Si il y a un message d'erreur 

			if( !$error){
				$returnuser = $user->save($params);
			}
			else{
				$returnuser = 0;
			}
			
			
			if($id != null){
				Utils::message($returnuser,'
				L\'utilisateur a été mis à jour avec succès',
				'L\'utilisateur n\'a pas pu être mis à jour.');
			}
			else{
				Utils::message($returnuser,'
				L\'utilisateur a été ajouté avec succès',
				'L\'utilisateur n\'a pas pu être ajouté.');
			}


		// 	// echo '<pre>';
		// 	// print_r($_POST['categ']);
		// 	// die();	
			if( $error ){
				$this->view->errNom = $nom;
				$this->view->errPrenom = $prenom;
				$this->view->errImg = $this->_getParam('image');
				$this->view->errPseudo = $pseudo;
				$this->view->errPass = $this->_getParam('pass');
				$this->view->errConfpass = $this->_getParam('confpass');
				$this->view->errAdrs = $adrsFacturation;
				$this->view->errAdrs2 = $adrsLivraison;
				$this->view->errMail = $mail;
			}
			else{
				$userLog = $user->fetchOne($returnuser);
				$_SESSION['user'] = $userLog;
				header('Location:'.WEB_ROOT.'/films');
				exit();	
			}

			

		}


	}

    public function loginAction(){

		$user = new User();


		if ($this->_request->isPost()){

			// On initialise les variables
			$pseudo = $this->_getParam('pseudo');
			$pass = $this->_getParam('pass');
			// On cherche à savoir si l'utilisateur existe
			if ($user->fetchPseudo($pseudo)){
				$userLog = $user->fetchPseudo($pseudo);

				// On check si le pass correspond à celui du user

				if($userLog->pass == md5($pass) )
				{
					$_SESSION['user'] = $userLog;
					$_SESSION['user']->basket = [];
					Utils::succesmsg('Connexion réussie.');
					header('Location:'.WEB_ROOT.'/films');
					exit();
				}
				else{
					Utils::errmsg('Le mot de passe est incorrect');
					$this->view->errPseudo = $pseudo;
				}
			}
			else{
				Utils::errmsg('Pseudo invalide');
			}
		}

    }

	public function logoutAction(){
		unset($_SESSION['user']); 
		header('Location:'.WEB_ROOT.'/films');
		Utils::succesmsg('Déconnexion réussie.');
		exit();

	}

	public function panierAction(){
		

	}


}

?>