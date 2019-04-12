<?php

	if (!empty($_POST['id_informe'])) {

        $id_informe = $_POST['id_informe'];

        $result = $this->db->dbRead("SELECT * FROM tb_informes WHERE informe_excluido = 0 AND id_informe = {$id_informe} LIMIT 1");

        if (is_array($result)) {

            $result2 = $this->db->dbRead("SELECT * FROM tb_informes_imagem WHERE imagem_excluida = 0 AND img_enviada = 1 AND id_informe = {$id_informe}");

            if (is_array($result2)) {

                $result[0]['Imagens'] = $result2;
            }

            $resultVeiculos = $this->db->dbRead("SELECT * FROM tb_veiculos INNER JOIN tb_informes_veiculo ON tb_veiculos.id_veiculo = tb_informes_veiculo.id_veiculo WHERE tb_informes_veiculo.veiculo_excluido = 0 AND tb_informes_veiculo.id_informe = {$id_informe}");

            if (is_array($resultVeiculos)) {

                $result[0]['Veiculos'] = $resultVeiculos;
            }

            $result3 = $this->db->dbRead("SELECT tb_pessoas.* FROM tb_informes_pessoa INNER JOIN tb_pessoas ON tb_informes_pessoa.id_pessoa = tb_pessoas.id_pessoa WHERE tb_informes_pessoa.pessoa_excluida = 0 AND tb_informes_pessoa.id_informe = {$id_informe}");

            if (is_array($result3)) {

                $result[0]['Pessoas'] = $result3;
            }

            $result4 = $this->db->dbRead("SELECT * FROM tb_informes_pessoa WHERE servidor_estadual = 1 AND pessoa_excluida = 0 AND id_informe = {$id_informe}");

            if (is_array($result4)) {

                $result[0]['Servidores'] = $result4;
            }

            $this->db->saspSuccess('Informe encontrado.', $result[0]);
        }
        else {

            $this->db->saspError('Informe não encontrado.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
