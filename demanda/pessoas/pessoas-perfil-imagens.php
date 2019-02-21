<?php

	if (!empty($_POST['id_pessoa'])) {

		$id_pessoa = $_POST['id_pessoa'];

        $result = $this->db->dbRead("SELECT img_busca, img_principal, cpf_usuario FROM tb_pessoas_imagem WHERE id_pessoa = {$id_pessoa} AND imagem_excluida = 0");

        if (is_array($result)) {

            $result_array = array('Resultado' => $result);

            $this->db->saspSuccess('Imagens encontradas.', $result_array);
        }
        else {

            $this->db->saspError('Nenhuma imagem.');
        }
	}
	else {

		$this->db->saspError('Acesso negado.');
	}

?>
