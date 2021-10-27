<?php
class User extends Model {

    public function __construct(){

        parent:: __construct();
        $this->_setTable('user');

    }

    public function fetchPseudo($pseudo)
	{
		$sql = 'select * from ' . $this->_table;
		$sql .= ' where pseudo = ?';
		
		$statement = $this->_dbh->prepare($sql);
		$statement->execute(array($pseudo));
		
		return $statement->fetch(PDO::FETCH_OBJ);
	}

}
?>