<?php

	if (!empty($_POST['id_pessoa'])) {

        $id_pessoa = $_POST['id_pessoa'];

        $result = $this->db->dbRead("SELECT * FROM tb_pessoas WHERE pessoa_excluida = 0 AND id_pessoa = {$id_pessoa} LIMIT 1");

        if (is_array($result)) {

            $result2 = $this->db->dbRead("SELECT * FROM tb_pessoas_imagem WHERE imagem_excluida = 0 AND img_enviada = 1 AND id_pessoa = {$id_pessoa}");

            if (is_array($result2)) {

                $result[0]['Imagens'] = $result2;
            }

            $result3 = $this->db->dbRead("SELECT tb_abordagens.*, ( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados FROM tb_abordagens INNER JOIN tb_abordagens_pessoa ON tb_abordagens.id_abordagem = tb_abordagens_pessoa.id_abordagem WHERE tb_abordagens_pessoa.id_pessoa = {$id_pessoa} ORDER BY tb_abordagens.data_registro DESC LIMIT 10");

            if (is_array($result3)) {

                $result[0]['Abordagens'] = $result3;
            }

            $result4 = $this->db->dbRead("SELECT * FROM tb_pessoas_atualizacoes WHERE id_pessoa = {$id_pessoa} AND atualizacao_excluida = 0 ORDER BY data_registro DESC LIMIT 5");

            if (is_array($result4)) {

                $result[0]['Atualizacoes'] = $result4;
            }

			$id_pessoa = $result[0]['id_pessoa'];

            $uf = array(

                '1' => 'AC',
                '2' => 'AL',
                '3' => 'AM',
                '4' => 'AP',
                '5' => 'BA',
                '6' => 'CE',
                '7' => 'DF',
                '8' => 'ES',
                '9' => 'GO',
                '10' => 'MA',
                '11' => 'MG',
                '12' => 'MS',
                '13' => 'MT',
                '14' => 'PA',
                '15' => 'PB',
                '16' => 'PE',
                '17' => 'PI',
                '18' => 'PR',
                '19' => 'RJ',
                '20' => 'RN',
                '21' => 'RO',
                '22' => 'RR',
                '23' => 'RS',
                '24' => 'SC',
                '25' => 'SE',
                '26' => 'SP',
                '27' => 'TO'
            );

            $total = count($result);

            for ($a = 0; $a < $total; $a++) {

                $uf_string = '';

                for ($b = 0; $b < 27; $b++) {

                    $shift = 1 << $b;

                    if (($result[$a]['areas_de_atuacao'] & $shift) > 0) {

                        $uf_string .= $uf[$b+1];
                        $uf_string .= ', ';
                    }
                }

                $uf_string = substr($uf_string, 0, -2);

                $result[$a]['areas_de_atuacao'] = $uf_string;
            }

			$this->db->dbExecute("UPDATE tb_pessoas SET num_visualizacoes = num_visualizacoes + 1 WHERE id_pessoa = {$id_pessoa} LIMIT 1");

            $this->db->saspSuccess('Perfil encontrado.', $result[0]);
        }
        else {

            $this->db->saspError('Perfil não encontrado.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
