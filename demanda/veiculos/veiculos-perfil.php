<?php

	if (!empty($_POST['id_veiculo'])) {

        $id_veiculo = $_POST['id_veiculo'];

        $result = $this->db->dbRead("SELECT * FROM tb_veiculos WHERE id_veiculo = {$id_veiculo} LIMIT 1");

        if (is_array($result)) {

            $result2 = $this->db->dbRead("SELECT * FROM tb_veiculos_imagem WHERE id_veiculo = {$id_veiculo}");

            if (is_array($result2)) {

                $result[0]['Imagens'] = $result2;
            }

            $placa = $result[0]['placa'];

            //( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem AND tb_abordagens_pessoa.pessoa_excluida = 0 ) as numero_abordados 

            $result3 = $this->db->dbRead("
            SELECT DISTINCT tb_abordagens.*, ( SELECT COUNT(id) FROM tb_abordagens_pessoa 
            WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem AND tb_abordagens_pessoa.pessoa_excluida = 0 ) as numero_abordados 
            FROM tb_abordagens 
            INNER JOIN tb_abordagens_pessoa ON tb_abordagens.id_abordagem = tb_abordagens_pessoa.id_abordagem 
            INNER JOIN tb_abordagens_veiculo ON tb_abordagens_veiculo.id_abordagem = tb_abordagens.id_abordagem 
            INNER JOIN tb_veiculos ON tb_abordagens_veiculo.id_veiculo = tb_veiculos.id_veiculo
            WHERE tb_veiculos.placa = '{$placa}' ORDER BY tb_abordagens.data_registro DESC LIMIT 50");

            if (is_array($result3)) {

                $result[0]['Abordagens'] = $result3;
            }

            $this->db->saspSuccess('Veículo encontrado.', $result[0]);
        }
        else {

            $this->db->saspError('Veículo não encontrado.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
