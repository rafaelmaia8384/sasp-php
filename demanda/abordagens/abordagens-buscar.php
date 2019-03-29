<?php

    if (!empty($_POST['latitude'])              &&
        !empty($_POST['longitude']) 		    &&
        !empty($_POST['nome_alcunha'])          &&
        !empty($_POST['distancia_maxima'])) {

        $conditions = '';
        $nome_alcunha_soundex = '';

        if ($_POST['nome_alcunha'] !== '-1') {

            $nome_alcunha = $_POST['nome_alcunha'];

            require 'sistema/MetaphonePTBR.php';

            $metaphone = new Metaphone();

            $nome_alcunha_soundex = $metaphone->getPhraseMetaphone($nome_alcunha);
            $nome_alcunha_soundex = getBooleanNames($nome_alcunha_soundex);
        }

        if ($_POST['latitude'] !== '-1' && $_POST['longitude'] !== '-1' && $_POST['distancia_maxima'] !== '-1') {

            $distancia = $_POST['distancia_maxima'];

            $latitude = str_replace(',', '.', $_POST['latitude']);
            $longitude = str_replace(',', '.', $_POST['longitude']);

            if ($distancia === '1') {

                $distancia = 1000;
            }
            elseif ($distancia === '2') {

                $distancia = 5000;
            }
            elseif ($distancia === '3') {
                
                $distancia = 10000;
            }
            elseif ($distancia === '4') {
                
                $distancia = 50000;
            }
            elseif ($distancia === '5') {
                
                $distancia = 100000;
            }
            elseif ($distancia === '6') {
                
                $distancia = 250000;
            }
            else {

                $distancia = 500000;
            }

            $result = $this->db->dbRead("SELECT *, ( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados, ST_distance_sphere(Point(latitude, longitude), Point({$latitude}, {$longitude})) as distancia FROM tb_abordagens HAVING distancia < {$distancia} AND abordagem_excluida = 0 ORDER BY data_registro DESC LIMIT 100");

            if (is_array($result)) {

                $abordagens = array('Resultado' => $result);

                $this->db->saspSuccess('Abordagens encontradas.', $abordagens);
            }
            else {

                $this->db->saspError('Nenhuma abordagem encontrada.');
            }
        }
        else {

            if ($_POST['nome_alcunha'] !== '-1') {

                //( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados

                $result = $this->db->dbRead("
                SELECT tb_abordagens.*, ( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados
                FROM tb_abordagens 
                INNER JOIN tb_abordagens_pessoa ON tb_abordagens.id_abordagem = tb_abordagens_pessoa.id_abordagem 
                INNER JOIN tb_pessoas ON tb_abordagens_pessoa.id_pessoa = tb_pessoas.id_pessoa 
                WHERE ( MATCH (tb_pessoas.nome_alcunha_soundex) AGAINST ('{$nome_alcunha_soundex}' IN BOOLEAN MODE) OR MATCH (tb_pessoas.nome_completo_soundex) AGAINST ('{$nome_alcunha_soundex}' IN BOOLEAN MODE) ) AND tb_abordagens.abordagem_excluida = 0 ORDER BY data_registro DESC LIMIT 100");

                if (is_array($result)) {

                    $abordagens = array('Resultado' => $result);

                    $this->db->saspSuccess('Abordagens encontradas.', $abordagens);
                }
                else {

                    $this->db->saspError('Nenhuma abordagem encontrada.');
                }
            }
            else {

                $this->db->saspError('Informações insuficientes.');
            }
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