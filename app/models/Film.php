<?php

class Film extends Model {

    public function __construct(){

        parent:: __construct();
        $this->_setTable('Film');

    }

    public function fetchName($search){

        $sql = 'SELECT * FROM ' . $this->_table . ' WHERE titre LIKE ?';

        $statement = $this->_dbh->prepare($sql);
        $statement->execute(array('%'.$search.'%'));

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function triParCateg(array $categ){

        $where='';
        
        if(count($categ)>0){
            foreach($categ as $key=>$categs)
                $where .= ' inner join filmCateg fc'.$key.' on ( fc'.$key.' .id_film = Film.id and fc'.$key.'.id_categ ='.$categs.')';
        }

        $sql = 'SELECT ' . $this->_table.'.id,'
        .$this->_table.'.image,'
        .$this->_table.'.titre,'
        .$this->_table.'.prix,'
        .$this->_table.'.date_sortie,'
        .$this->_table.'.description,'
        .$this->_table.'.realisateur'
        .' FROM '.$this->_table;
        $sql .= ' '.$where.';';

        // print_r($sql);

        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}
?>