<?php

    if (!empty($_POST['index']) &&
        !empty($_POST['cpf']) &&
        !empty($_POST['nome_completo'])) {
        
            $index = $_POST['index'];
            $cpf_pessoa = $_POST['cpf'];
            $nome_completo = $_POST['nome_completo'];

            $limit = ($index - 1) * $search_limit;

            require 'sistema/MetaphonePTBR.php';
            $metaphone = new Metaphone();

            $nome_completo_soundex = $metaphone->getPhraseMetaphone($nome_completo);
            $nome_completo_soundex = getBooleanNames($nome_completo_soundex);

            $conditions = '';

            if ($cpf_pessoa === '-1' && $nome_completo === '-1') {

                $this->db->saspError('Informações insuficientes.');
            }

            if ($cpf_pessoa !== '-1') {

                $conditions .= " cpf = {$cpf_pessoa} ";
            }

            if ($nome_completo !== '-1') {

                if ($cpf_pessoa !== '-1') {

                    $conditions .=  " OR MATCH (nome_completo_soundex) AGAINST ('{$nome_completo_soundex}' IN BOOLEAN MODE)";
                }
                else {

                    $conditions .=  " MATCH (nome_completo_soundex) AGAINST ('{$nome_completo_soundex}' IN BOOLEAN MODE)";
                }
            }
        
            $result = $this->db->dbRead("SELECT img_principal, img_busca, id_pessoa, nome_alcunha, nome_completo, areas_de_atuacao, data_registro FROM tb_pessoas WHERE pessoa_excluida = 0 AND ( {$conditions} ) ORDER BY ( ( num_visualizacoes * 100 ) / DATEDIFF(NOW(), data_registro) ) DESC LIMIT {$limit}, {$search_limit}");

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