<?php

	if (!empty($_POST['id_pessoa'])) {

        $id_pessoa = $_POST['id_pessoa'];
        
        // -------------------------------------------------------------------
            // -------------------------------------------------------------------
            //         MUDAR A LINHA ABAIXO PARA TB_PESSOAS
            // -------------------------------------------------------------------
            // -------------------------------------------------------------------

        $result = $this->db->dbRead("SELECT * FROM tb_pessoas_test WHERE pessoa_excluida = 0 AND id_pessoa = {$id_pessoa} LIMIT 1");

        if (is_array($result)) {

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

                $result[$a]['protect_hash'] = '';

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
