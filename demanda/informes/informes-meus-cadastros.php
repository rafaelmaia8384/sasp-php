<?php

	if (!empty($_POST['index'])) {

		$index = $_POST['index'];
        $limit = ($index - 1) * $search_limit;

        $result = $this->db->dbRead("SELECT * FROM tb_informes WHERE cpf_usuario = {$cpf} AND informe_excluido = 0 ORDER BY data_registro DESC LIMIT {$limit}, {$search_limit}");

        if (is_array($result)) {

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Informes encontrados.', $result_array);
        }
        else {

            $this->db->saspError('Nenhum informe enviado por você.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
