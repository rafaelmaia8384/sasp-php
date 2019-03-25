<?php

    if (!empty($_POST['latitude'])              &&
        !empty($_POST['longitude']) 		    &&
        !empty($_POST['nome_alcunha'])          &&
        !empty($_POST['distancia_maxima'])) {

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

            $result = $this->db->dbRead("SELECT *, ( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados, ST_distance_sphere(Point(latitude, longitude), Point({$latitude}, {$longitude})) as distancia FROM tb_abordagens HAVING distancia < {$distancia} AND abordagem_excluida = 0 ORDER BY data_registro LIMIT 100");

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

                //implementar isso!!!
                $this->db->saspError('Falta implementar!.');
            }
            else {

                $this->db->saspError('Informações insuficientes.');
            }
        }
        
        $this->db->saspSuccess('Ok');
    }
    else {

        $this->db->saspError('Acesso negado.');
    }

?>