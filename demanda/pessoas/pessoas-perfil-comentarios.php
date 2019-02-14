<?php

	if (!empty($_POST['id_pessoa']) &&
        !empty($_POST['index'])) {

        $id_pessoa = $_POST['id_pessoa'];
        $index = $_POST['index'];

        $limit = ($index - 1) * $search_limit;

        $result = $this->db->dbRead("SELECT tb_pessoas_comentario.id as id, tb_pessoas_comentario.cpf_usuario as cpf_usuario, tb_pessoas_comentario.comentario as comentario, tb_pessoas_comentario.data_registro as data_registro, tb_usuarios.img_busca as img_busca FROM ".
        "tb_pessoas_comentario INNER JOIN tb_usuarios ON tb_pessoas_comentario.cpf_usuario = tb_usuarios.cpf ".
        "WHERE tb_pessoas_comentario.id_pessoa = {$id_pessoa} AND tb_pessoas_comentario.item_excluido = 0 ORDER BY tb_pessoas_comentario.data_registro DESC LIMIT {$limit}, {$search_limit}");

        if (is_array($result)) {

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Comentários encontrados.', $result_array);
        }
        else {

            $this->db->saspError('Nenhum comentário.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
