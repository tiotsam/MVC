<?php
class Categorie extends Model {

    public function __construct(){

        parent:: __construct();
        $this->_setTable('categorie');

    }

    public function categorieFilm($idFilm,$fetchType=PDO::FETCH_OBJ){

        $sql = 'select categorie.id, categorie.nom from categorie inner join filmCateg ON categorie.id = filmCateg.id_categ where filmCateg.id_film='.$idFilm;
        
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll($fetchType);
    }
}
?>