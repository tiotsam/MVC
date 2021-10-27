<?php 

class AjaxController extends Controller{

    public function updatecategorieAction(){
        $nom = $this->_getParam('categorie');
        $id = $this->_getParam('id');
        $alias = Utils::generateSlug($nom);

        $params = [
            'id' => $id,
            'nom' => $nom,
            'slug' => $alias
        ];

        $cat = new Categorie();

        $return = $cat->save($params);
        echo $return;
        die();
    }

    public function searchajaxparcategorieAction(){

        // print_r($this->_getParam('categTri'));

        $film = new Film();	
		$categ = new Categorie();
		$filmCategs = new Filmcateg();

        $search = $this->_getParam('categTri');
        
        if ($search == null){
            $films = $film->fetchAll();
        }
        else{
            $films = $film->triParCateg($search) ;
        }

        if ($films != null){
            foreach ($films as $value) {
                $categs[$value->id] = $categ->categorieFilm($value->id);
            }
            $this->view->categs = $categs;
        }
        
        $this->view->films = $films;
        $this->view->disableLayout();

    }

    public function addpanierAction(){
        
        $film = new Film();
        $exist = false;

        $filmID = $this->_getParam('idFilm');

        $filmPanier = $film->fetchOne($filmID);


        
        if( count($_SESSION['user']->basket) > 0 ){

            foreach ($_SESSION['user']->basket as $value) {

                if( $value->id == $filmID ){
                    $value->quantity++;
                    $exist = true;
                }
            }

            if ( !$exist ){

                $filmPanier->quantity = 1;
                array_push($_SESSION['user']->basket,$filmPanier);

            }

        }
        else{
            $filmPanier->quantity = 1;
            array_push($_SESSION['user']->basket,$filmPanier);
        }

        echo '<pre>';
        print_r($_SESSION['user']->basket);

        die();
      
    }

    public function minpanierAction(){
        
        $film = new Film();
        $exist = false;

        $filmID = $this->_getParam('idFilm');

        $filmPanier = $film->fetchOne($filmID);
        
        if( count($_SESSION['user']->basket) > 0 ){

            foreach ($_SESSION['user']->basket as $key => $value) {

                if( $value->id == $filmID ){
                    $value->quantity--;

                    if($value->quantity <= 0){
                        unset($_SESSION['user']->basket[$key]);
                    }
                    $exist = true;
                }
            }

            if ( !$exist ){
                $filmPanier->quantity = 1;
                array_push($_SESSION['user']->basket,$filmPanier);
            }

        }
        else{
            $filmPanier->quantity = 1;
            array_push($_SESSION['user']->basket,$filmPanier);
        }
        


        die();      
    }


}

?>