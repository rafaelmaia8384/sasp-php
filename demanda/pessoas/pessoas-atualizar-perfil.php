<?php

    if (!empty($_POST['id_pessoa'])) {


        //
        //
        //  IMPLEMENTAR ATUALIZACAO DA AREA DE ATUACAO
        //
        //

        $id_pessoa = $_POST['id_pessoa'];

        $atualizacao = array(

            'id_pessoa' => $id_pessoa,
            'cpf_usuario' => $cpf
        );

        require 'sistema/MetaphonePTBR.php';
        $metaphone = new Metaphone();

        if (!empty($_POST['alcunha'])) {

            $atualizacao['alcunha'] = ucwords($_POST['alcunha']);
            $alcunha_soundex = $metaphone->getPhraseMetaphone($_POST['alcunha']);
            $atualizacao['alcunha_soundex'] = $alcunha_soundex;
        }

        if (!empty($_POST['nome_completo'])) {

            $atualizacao['nome_completo'] = ucwords($_POST['nome_completo']);
            $nome_completo_soundex = $metaphone->getPhraseMetaphone($_POST['nome_completo']);
            $atualizacao['nome_completo_soundex'] = $nome_completo_soundex;
        }

        if (!empty($_POST['nome_da_mae'])) {

            $atualizacao['nome_da_mae'] = ucwords($_POST['nome_da_mae']);
            $nome_da_mae_soundex = $metaphone->getPhraseMetaphone($_POST['nome_da_mae']);
            $atualizacao['nome_da_mae_soundex'] = $nome_da_mae_soundex;
        }

        if (!empty($_POST['cpf'])) {

            $atualizacao['cpf'] = $_POST['cpf'];
        }

        if (!empty($_POST['rg'])) {

            $atualizacao['rg'] = $_POST['rg'];
        }

        if (!empty($_POST['data_nascimento'])) {

            $atualizacao['data_nascimento'] = $_POST['data_nascimento'];
        }

        $result = $this->db->dbRead("SELECT id FROM tb_pessoas_atualizacoes WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");

        if (is_array($result)) {

            if (array_key_exists('alcunha', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET alcunha = '{$atualizacao['alcunha']}', alcunha_soundex = '{$atualizacao['alcunha_soundex']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            if (array_key_exists('nome_completo', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET nome_completo = '{$atualizacao['nome_completo']}', nome_completo_soundex = '{$atualizacao['nome_completo_soundex']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            if (array_key_exists('nome_da_mae', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET nome_da_mae = '{$atualizacao['nome_da_mae']}', nome_da_mae_soundex = '{$atualizacao['nome_da_mae_soundex']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            if (array_key_exists('cpf', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET cpf = '{$atualizacao['cpf']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            if (array_key_exists('rg', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET rg = '{$atualizacao['rg']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            if (array_key_exists('data_nascimento', $atualizacao)) {

                $this->db->dbExecute("UPDATE tb_pessoas_atualizacoes SET data_nascimento = '{$atualizacao['data_nascimento']}' WHERE cpf_usuario = {$cpf} AND id_pessoa = {$id_pessoa} LIMIT 1");
            }

            $this->db->saspSuccess('Informações atualizadas.', $atualizacao);
        }
        else {

            $this->db->dbInsert('tb_pessoas_atualizacoes', $atualizacao);

            $this->db->saspSuccess('Informações atualizadas.', $atualizacao);
        }
    }
    else {

        $this->db->saspError('Acesso negado.');
    }

?>