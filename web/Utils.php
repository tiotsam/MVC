<?php 

class Utils {
    
    
    static function generateSlug($value){
        $string = preg_replace("/['’]/", ' ', $value);
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);
        $string = preg_replace('/[-\s]+/', '-', $string);
        return trim($string, '-');
    }

    public static function message($save,$succes,$err){
        if($save){
            $_SESSION['message']['succes'][]=$succes;
            return true; 
        }                  
        else{
            $_SESSION['message']['fail'][]=$err;
            return false;
        }                            
    }

    public static function upload($index,$destination,$maxsize=FALSE,$extensions=FALSE){
        // fichier correctement uploadé
        if (!isset($_FILES[$index]) OR $_FILES[$index]['error'] > 0) return FALSE;
        // test taille limite
        if ($maxsize !== FALSE AND $_FILES[$index]['size'] > $maxsize) return FALSE;
        // test extension
        $ext = substr(strrchr($_FILES[$index]['name'],'.'),1);
        if (!$extensions !== FALSE AND !in_array($ext,$extensions)) return FALSE;
        // Déplacement
        return move_uploaded_file($_FILES[$index]['tmp_name'],$destination);
    }

    public static function errmsg($msg){

        $_SESSION['message']['fail'][] = $msg;

    }

    public static function succesmsg($msg){

        $_SESSION['message']['succes'][] = $msg;

    }
}

?>