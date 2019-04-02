<?php

	if (!empty($_POST['index'])) {

		$index = $_POST['index'];
        $limit = ($index - 1) * $search_limit;

        $matricula = 0;

        $result = $this->db->dbRead("SELECT matricula FROM tb_usuarios WHERE cpf = {$cpf} AND usuario_excluido = 0 LIMIT 1");

        if (is_array($result)) {

            $matricula = $result[0]['matricula'];
        }
        else {

            $this->db->saspError('Matrícula não encontrada.');
        }

        $result = $this->db->dbRead("
        SELECT DISTINCT tb_abordagens.*, ( SELECT COUNT(id) FROM tb_abordagens_pessoa WHERE tb_abordagens_pessoa.id_abordagem = tb_abordagens.id_abordagem ) as numero_abordados
        FROM tb_abordagens 
        INNER JOIN tb_abordagens_matricula ON tb_abordagens.id_abordagem = tb_abordagens_matricula.id_abordagem
        WHERE tb_abordagens_matricula.matricula = {$matricula} AND tb_abordagens.abordagem_excluida = 0 
        ORDER BY tb_abordagens.data_registro DESC LIMIT {$limit}, {$search_limit}");

        if (is_array($result)) {

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Abordagens encontradas.', $result_array);
        }
        else {

            $this->db->saspError('Você não participou de nenhuma abordagem.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
