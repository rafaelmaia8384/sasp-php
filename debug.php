<?php

    require 'sistema/Config.php';
    require 'sistema/Database.php';
    require 'sistema/Utils.php';
    require 'sistema/MetaphonePTBR.php';

    $db = new DataBase();
    $metaphone = new Metaphone();
    
    $result = $db->dbRead("SELECT * FROM tb_pessoas WHERE id > 0");
    
    if (is_array($result)) {

        for ($i = 0; $i < count($result); $i++) {

            $nome_da_mae = $result[$i]['nome_da_mae'];

            if ($nome_da_mae === '') continue;

            $nome_da_mae_soundex = $metaphone->getPhraseMetaphone($nome_da_mae);
            $id_pessoa = $result[$i]['id_pessoa'];

            $db->dbExecute("UPDATE tb_pessoas SET nome_da_mae_soundex = '{$nome_da_mae_soundex}' WHERE id_pessoa = {$id_pessoa} LIMIT 1");
        }

        $db->saspSuccess('OK');
    }
    else {

        $db->saspError('DEBUG');
    }
    
 ?>
