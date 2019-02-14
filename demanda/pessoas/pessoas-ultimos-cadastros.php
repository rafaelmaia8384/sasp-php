<?php

	if (!empty($_POST['index']) 		&&
		!empty($_POST['date_time_max'])) {

		$index = $_POST['index'];
		$date_time_max = $_POST['date_time_max'];

        $limit = ($index - 1) * $search_limit;

        $result = $this->db->dbRead("SELECT * FROM tb_pessoas WHERE pessoa_excluida = 0 AND data_registro < '$date_time_max' ORDER BY data_registro DESC LIMIT {$limit}, {$search_limit}");

        if (is_array($result)) {

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

                if (strlen($result[$a]['nome_completo']) > 0 && $result[$a]['nome_completo'] !== $result[$a]['nome_alcunha']) {

                    $result[$a]['nome_alcunha'] = $result[$a]['nome_completo'] . " ({$result[$a]['nome_alcunha']})";
                }
            }

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Pessoas encontradas.', $result_array);
        }
        else {

            $this->db->saspError('Nenhum resultado.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
