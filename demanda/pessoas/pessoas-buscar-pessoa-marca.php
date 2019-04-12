<?php

    if (!empty($_POST['index']) &&
        !empty($_POST['tipo_marca']) &&
        !empty($_POST['descricao']) &&
        !empty($_POST['parte_corpo'])) {
        
            $index = $_POST['index'];
            $tipo_marca = $_POST['tipo_marca'];
            $descricao = $_POST['descricao'];
            $parte_corpo = $_POST['parte_corpo'];

            $limit = ($index - 1) * $search_limit;

            $conditions = '';

            if ($descricao !== 'null') {

                require 'sistema/MetaphonePTBR.php';
                $metaphone = new Metaphone();

                $descricao = $metaphone->getPhraseMetaphone($descricao);
                $descricao = getBooleanNames($descricao);

                $conditions .= " AND MATCH (descricao_soundex) AGAINST ('{$descricao}' IN BOOLEAN MODE) ";
            }

            $result = $this->db->dbRead(
                "SELECT DISTINCT tb_pessoas.* FROM tb_pessoas INNER JOIN tb_pessoas_marca_corporal ON tb_pessoas.id_pessoa = tb_pessoas_marca_corporal.id_pessoa ".
                "WHERE tb_pessoas.pessoa_excluida = 0 AND tb_pessoas_marca_corporal.marca_excluida = 0 AND tb_pessoas_marca_corporal.marca_tipo = {$tipo_marca} AND parte_corpo = {$parte_corpo} {$conditions}".
                "ORDER BY ( ( tb_pessoas.num_visualizacoes * 100 ) / DATEDIFF(NOW(), tb_pessoas.data_registro) ) DESC LIMIT {$limit}, {$search_limit}"
            );

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

                $pessoas = array('Resultado' => $result);

                $this->db->saspSuccess('Pessoas encontradas.', $pessoas);
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