<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller 
{

    public function addcategAction()
	{
		
		// if(isset($_POST['categorie']) && $_POST['categorie'] != null){
		if($this->_request->isPost() && $_POST['categorie'] != null){
			$nom = $_POST['categorie'];
			$alias = Utils::generateSlug($nom);

			$params = [
				'nom' => $nom,
				'slug' => $alias
			];

			$cat = new Categorie();

			// $this->view->insertReturn = $cat->save($params);
			Utils::message($cat->save($params),'
			La catégorie a été ajoutée avec succès',
			'La catégorie n\'a pas pu être ajouté.');
			
			header('Location:'.WEB_ROOT.'/categories');
			exit();
		}

	}

	public function categoriesAction(){

		$cat = new Categorie();
		
		$delete = $this->_getParam('iddelete');
		if($delete != ''){
			Utils::message($cat->delete($delete),
				'La catégorie a été supprimmée avec succès',
				'La catégorie n\'a pas pu être supprimée');
		}		
		
		$categs = $cat->fetchAll('nom asc');
		$this->view->categs = $categs;


	}

	public function updatecategAction(){
		
		$id = $this->_getParam('id');

		$cat = new Categorie();
		$categs = $cat->fetchOne($id);
		$this->view->categs = $categs;

		// if($this->_request->isPost() && $_POST['categorie'] != null){
		// 	$nom = $_POST['categorie'];
		// 	$alias = Utils::generateSlug($nom);

		// 	$params = [
		// 		'id' => $id,
		// 		'nom' => $nom,
		// 		'slug' => $alias
		// 	];

		// 	$cat = new Categorie();

		// 	Utils::message($cat->save($params),'
		// 	La catégorie a été modifiée avec succès',
		// 	'La catégorie n\'a pas pu être modifiée.');
			
		// 	header('Location:'.WEB_ROOT.'/categories');
		// 	exit();
		// }
		
	}

	public function addfilmAction()
	{
		// Initialisation des variables 
		$cat = new Categorie();
		$film = new Film();
		$filmCategs = new Filmcateg();
		

		$categs = $cat->fetchAll('nom asc');
		$this->view->categs = $categs;
		// Gestion de l'update
		$id = $this->_getParam('id');

		if($id != null){

			// On remplit le formulaire
			$filmUpdate = $film->fetchOne($id);
			$this->view->filmUpdate = $filmUpdate;
			$categUpdate[$id] = $cat->categorieFilm($id);
			$this->view->categUpdate = $categUpdate;
			
		}

		// Gestion de l'envoi du formulaire
		if($this->_request->isPost()){
			$titre = $this->_getParam('titre');
			$alias = Utils::generateSlug($titre);
			$dateSortie = $this->_getParam('dateSortie');
			$desc = $this->_getParam('description');
			$prix = $this->_getParam('prix');
			$real = $this->_getParam('realisateur');

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
				Utils::upload('image','../web/images/'.$titleImg,FALSE,$extensions);
				$img = 'images/'.$titleImg;

			}
			else{
				if($id != null){
					$img = $this->_getParam('imageUpdate');
				}
					
			}

			if ($id != null){
				$params = [
					'id' => $id,
					'titre' => $titre,
					'slug' => $alias,
					'date_sortie' => $dateSortie,
					'description' => $desc,
					'image' => $img,
					'prix' => $prix,
					'realisateur' => $real
				];
			}else{
				$params = [
					'titre' => $titre,
					'slug' => $alias,
					'date_sortie' => $dateSortie,
					'description' => $desc,
					'image' => $img,
					'prix' => $prix,
					'realisateur' => $real
				];
			}

			$returnfilm = $film->save($params);

			if($id != null){
				Utils::message($returnfilm,'
				Le film a été mis à jour avec succès',
				'Le film n\'a pas pu être mis à jour.');
			}
			else{
				Utils::message($returnfilm,'
				Le film a été ajouté avec succès',
				'Le film n\'a pas pu être ajouté.');
			}


			// On gère les catégories
			if($id != null){
				// On supprime les catégories existantes pour ce film
				$filmCategs->deleteCategFilm($id);
				// On enregistre les catégories entrés dans le formulaire
				foreach($_POST['categ'] as $categorie){
					$params2 = [
						'id_film' => $id,
						'id_categ' => $categorie
					];
					
					$filmCategs->save($params2);
				}
			}else{
				foreach($_POST['categ'] as $categorie){
					$params2 = [
						'id_film' => $returnfilm,
						'id_categ' => $categorie
					];
					
					$filmCategs->save($params2);
				}
			}


			// echo '<pre>';
			// print_r($_POST['categ']);
			// die();	

			header('Location:'.WEB_ROOT.'/films');
			exit();	

		}


	}

	public function filmsAction(){

		$film = new Film();	
		$categ = new Categorie();
		$filmCategs = new Filmcateg();
		
		$delete = $this->_getParam('iddelete');

		if($delete != ''){

			$filmCategs->deleteCategFilm($delete);
			$filmDelete = $film->fetchOne($delete);
			unlink($filmDelete->image);

			Utils::message($film->delete($delete),
				'Le film a été supprimmé avec succès',
				'Le film n\'a pas pu être supprimé');
		}		
		
		// On gère la barre de recherche 
		$search = $this->_getParam('search');

		if($search == '' || $search == null ){
			// si il n'y a pas de recherche on affiche tous les films
			$films = $film->fetchAll('titre asc');
		}
		else{
			// Si on a fait une recherche on affiche les films avec le nom correspondant
						
			if(Utils::message($film->fetchName($search),'
			Le film a été trouvé avec succès',
			'Le film n\'a pas été trouvé.')){
				$films = $film->fetchName($search);
			}else{
				// Si il n'y a pas de film avec le nom correspondant on affiche tout
				$films = $film->fetchAll('titre asc');
			}
		}
		
		$this->view->films = $films;


		foreach ($films as $value) {
			$categs[$value->id] = $categ->categorieFilm($value->id);
		}

		$allCategs = $categ->fetchAll('nom asc');

		$this->view->allCategs = $allCategs;
		$this->view->categs = $categs;
	}
	
	public function detailfilmAction(){

		$film = new Film();	
		$categ = new Categorie();
		$filmCateg = new Filmcateg();
		$id = $this->_getParam('idread');

		$filmDetail = $film->fetchOne($id);
		$this->view->filmDetail = $filmDetail;

		$filmCategs = $categ->categorieFilm($id);
		$this->view->filmCategs = $filmCategs;

	}
}
