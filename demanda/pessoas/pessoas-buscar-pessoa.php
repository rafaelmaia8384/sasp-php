<?php

if (!empty($_POST['index'])                 &&
    !empty($_POST['nome_alcunha']) 		    &&
    !empty($_POST['areas_de_atuacao'])      &&
    !empty($_POST['historico_criminal'])    &&
    !empty($_POST['crt_cor_pele'])          &&
    !empty($_POST['crt_cor_olhos'])         &&
    !empty($_POST['crt_cor_cabelos'])       &&
    !empty($_POST['crt_tipo_cabelos'])      &&
    !empty($_POST['crt_porte_fisico'])      &&
    !empty($_POST['crt_estatura'])          &&
    !empty($_POST['crt_deficiente'])        &&
	!empty($_POST['crt_tatuagem'])) {

        $index = $_POST['index'];
        $nome_alcunha = $_POST['nome_alcunha'];
        $areas_de_atuacao = $_POST['areas_de_atuacao'];
        $historico_criminal = $_POST['historico_criminal'];

        $crt_cor_pele = $_POST['crt_cor_pele'];
        $crt_cor_olhos = $_POST['crt_cor_olhos'];
        $crt_cor_cabelos = $_POST['crt_cor_cabelos'];
        $crt_tipo_cabelos = $_POST['crt_tipo_cabelos'];
        $crt_porte_fisico = $_POST['crt_porte_fisico'];
        $crt_estatura = $_POST['crt_estatura'];
        $crt_deficiente = $_POST['crt_deficiente'];
        $crt_tatuagem = $_POST['crt_tatuagem'];

        $limit = ($index - 1) * $search_limit;

        $conditions = '';

        if ($nome_alcunha != '$%null%$') {

            require 'sistema/MetaphonePTBR.php';

            $metaphone = new Metaphone();

            $nome_alcunha_soundex = $metaphone->getPhraseMetaphone($nome_alcunha);
            $nome_alcunha_soundex = getBooleanNames($nome_alcunha_soundex);

            $conditions .= " AND ( MATCH (nome_alcunha_soundex) AGAINST ('{$nome_alcunha_soundex}' IN BOOLEAN MODE) OR MATCH (nome_completo_soundex) AGAINST ('{$nome_alcunha_soundex}' IN BOOLEAN MODE) )";
        }

        if ($areas_de_atuacao != '-1') {

            $conditions .= " AND areas_de_atuacao & {$areas_de_atuacao} > 0";
        }

        if ($historico_criminal != '-1') {

            $conditions .= " AND historico_criminal & {$historico_criminal} > 0";
        }

        if ($crt_cor_pele > 1) {

            $conditions .= " AND crt_cor_pele = {$crt_cor_pele}";
        }

        if ($crt_cor_olhos > 1) {

            $conditions .= " AND crt_cor_olhos = {$crt_cor_olhos}";
        }

        if ($crt_cor_cabelos > 1) {

            $conditions .= " AND crt_cor_cabelos = {$crt_cor_cabelos}";
        }

        if ($crt_tipo_cabelos > 1) {

            $conditions .= " AND crt_tipo_cabelos = {$crt_tipo_cabelos}";
        }

        if ($crt_porte_fisico > 1) {

            $conditions .= " AND crt_porte_fisico = {$crt_porte_fisico}";
        }

        if ($crt_estatura > 1) {

            $conditions .= " AND crt_estatura = {$crt_estatura}";
        }

        if ($crt_deficiente > 1) {

            $conditions .= " AND crt_possui_deficiencia = {$crt_deficiente}";
        }

        if ($crt_tatuagem > 1) {

            $conditions .= " AND crt_possui_tatuagem = {$crt_tatuagem}";
        }

        if ($conditions == '') {

            $conditions = 'AND id > 0';
        }

        $result = $this->db->dbRead("SELECT img_principal, img_busca, id_pessoa, nome_alcunha, nome_completo, areas_de_atuacao, data_registro FROM tb_pessoas WHERE pessoa_excluida = 0 {$conditions} ORDER BY ( ( num_visualizacoes * 100 ) / DATEDIFF(NOW(), data_registro) ) DESC LIMIT {$limit}, {$search_limit}");

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

			//DBExecuteNoError("UPDATE tb_usuarios SET total_buscas = total_buscas + 1 WHERE id_usuario = {$id_usuario} AND usuario_excluido = 0 LIMIT 1");

            $this->db->saspSuccess('Busca realizada.', $result_array);
        }
        else {

            $this->db->saspError('Nenhuma pessoa encontrada.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
    }
    
    function getBooleanNames($text) {

		$boolean_names = '';
		$nomes = explode(' ', $text);
		$count = count($nomes);

		for ($a = 0; $a < $count; $a++) {

			$soundex = $nomes[$a];
			$boolean_names .= "+{$soundex} ";
		}

		return trim($boolean_names);
	}

?>
