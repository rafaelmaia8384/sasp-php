<?php

	if (!empty($_POST['id_abordagem'])) {

        $id_abordagem = $_POST['id_abordagem'];

        $result = $this->db->dbRead("SELECT * FROM tb_abordagens WHERE abordagem_excluida = 0 AND id_abordagem = {$id_abordagem} LIMIT 1");

        if (is_array($result)) {

            $result2 = $this->db->dbRead("SELECT * FROM tb_abordagens_imagem WHERE imagem_excluida = 0 AND img_enviada = 1 AND id_abordagem = {$id_abordagem}");

            if (is_array($result2)) {

                $result[0]['Imagens'] = $result2;
            }

            $resultVeiculos = $this->db->dbRead("SELECT * FROM tb_veiculos INNER JOIN tb_abordagens_veiculo ON tb_veiculos.id_veiculo = tb_abordagens_veiculo.id_veiculo WHERE tb_abordagens_veiculo.veiculo_excluido = 0 AND tb_abordagens_veiculo.id_abordagem = {$id_abordagem}");

            if (is_array($resultVeiculos)) {

                $result[0]['Veiculos'] = $resultVeiculos;
            }

            $result3 = $this->db->dbRead("SELECT tb_pessoas.* FROM tb_abordagens_pessoa INNER JOIN tb_pessoas ON tb_abordagens_pessoa.id_pessoa = tb_pessoas.id_pessoa WHERE tb_abordagens_pessoa.pessoa_excluida = 0 AND tb_abordagens_pessoa.id_abordagem = {$id_abordagem}");

            if (is_array($result3)) {

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
    
                $total = count($result3);
    
                for ($a = 0; $a < $total; $a++) {
    
                    $uf_string = '';
    
                    for ($b = 0; $b < 27; $b++) {
    
                        $shift = 1 << $b;
    
                        if (($result3[$a]['areas_de_atuacao'] & $shift) > 0) {
    
                            $uf_string .= $uf[$b+1];
                            $uf_string .= ', ';
                        }
                    }
    
                    $uf_string = substr($uf_string, 0, -2);
    
                    $result3[$a]['areas_de_atuacao'] = $uf_string;
                }

                $result[0]['Pessoas'] = $result3;
            }

            $this->db->saspSuccess('Abordagem encontrada.', $result[0]);
        }
        else {

            $this->db->saspError('Abordagem não encontrada.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
