<?php

class Filmcateg extends Model {

    public function __construct(){

        parent:: __construct();
        $this->_setTable('filmCateg');
    }

    public function deleteCategFilm($id){
        $sql = 'delete from filmCateg where id_film='.$id;
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
    }

}
?>