<?php

	if (!empty($_POST['index']) 		&&
		!empty($_POST['date_time_max'])) {

		$index = $_POST['index'];
		$date_time_max = $_POST['date_time_max'];

        $limit = ($index - 1) * $search_limit;

        $result = $this->db->dbRead("SELECT * FROM tb_abordagens WHERE abordagem_excluida = 0 AND data_registro < '$date_time_max' ORDER BY data_registro DESC LIMIT {$limit}, {$search_limit}");

        if (is_array($result)) {

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Abordagens encontradas.', $result_array);
        }
        else {

            $this->db->saspError('Nenhuma abordagem cadastrada.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
