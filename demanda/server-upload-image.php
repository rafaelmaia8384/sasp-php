<?php

    require './sistema/Config.php';
    require './vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    if (!empty($_POST['modulo'])) {

        $modulo = $_POST['modulo'];

        if (!empty($_FILES['img_busca'])) {
    
            if ($_FILES['img_busca']['error'] != 0) {
    
                $this->db->saspError('Erro no envio da imagem. Tente novamente mais tarde.');
            }
    
            if (($_FILES['img_busca']['size'] / 1024) > FILE_SIZE_UPLOAD_MAX) {
    
                $this->db->saspError('O tamanho máximo da imagem deve ser de '. FILE_SIZE_UPLOAD_MAX .'kB.');
            }
        }
        else {
    
            $this->db->saspError('Imagem não anexada.');
        }

        if (!empty($_FILES['img_principal'])) {

            if ($_FILES['img_principal']['error'] != 0) {
    
                $this->db->saspError('Erro no envio da imagem. Tente novamente mais tarde.');
            }
    
            if (($_FILES['img_principal']['size'] / 1024) > FILE_SIZE_UPLOAD_MAX) {
    
                $this->db->saspError('O tamanho máximo da imagem deve ser de '. FILE_SIZE_UPLOAD_MAX .'kB.');
            }
        }
        else {
    
            $this->db->saspError('Imagem não anexada.');
        }
    
        $imgBuscaName = $_FILES['img_busca']['name'];
        $imgPrincipalName = $_FILES['img_principal']['name'];

        $buscaFolder = utilsGetSaspImgFolder($modulo, true);
        $principalFolder = utilsGetSaspImgFolder($modulo, false);

        if (!is_dir($buscaFolder) || !is_dir($principalFolder)) {

            $this->db->saspError('Módulo inválido.');
        }
    
        $imgBuscaPath = $buscaFolder . $imgBuscaName;
        $imgPrincipalPath = $principalFolder . $imgPrincipalName;

        if ($modulo === 'pessoas') {

            $result1 = $this->db->dbRead("SELECT id FROM tb_pessoas WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
            $result2 = $this->db->dbRead("SELECT id FROM tb_pessoas_imagem WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        
            if (!is_array($result1) && !is_array($result2)) {

                $this->db->saspError('Imagem não cadastrada no banco de dados.');
            }
        }
        else if ($modulo === 'abordagens') {

            $result1 = $this->db->dbRead("SELECT id FROM tb_abordagens WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
            $result2 = $this->db->dbRead("SELECT id FROM tb_abordagens_imagem WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        
            if (!is_array($result1) && !is_array($result2)) {

                $this->db->saspError('Imagem não cadastrada no banco de dados.');
            }
        }
        else if ($modulo === 'veiculos') {
            
            $result1 = $this->db->dbRead("SELECT id FROM tb_veiculos_imagem WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");

            if (!is_array($result1)) {

                $this->db->saspError('Imagem não cadastrada no banco de dados.');
            }
        }
        else if ($modulo === 'informes') {
            
            $result1 = $this->db->dbRead("SELECT id FROM tb_informes_imagem WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");

            if (!is_array($result1)) {

                $this->db->saspError('Imagem não cadastrada no banco de dados.');
            }
        }
        else {

            $this->db->saspError('Módulo não encontrado.');
        }

        //Enviar imagens ao CEPH: bucket 'sasp'

        try {
            
            $s3 = S3Client::factory(
                array(
                    'credentials' => array(
                        'key' => AWS_KEY,
                        'secret' => AWS_SECRET_KEY
                    ),
                    'version' => 'latest',
                    'region'  => 'us-east-1',
                    'use_path_style_endpoint' => true,
                    'endpoint' => 'https://ceph.apps.pm.pb.gov.br'
                )
            );
            
            $s3->putObject(array(

                'Bucket' => AWS_BUCKET_SASP,
                'Key'    => $imgBuscaName,
                'SourceFile'   => $_FILES['img_busca']['tmp_name'],
                'ACL'    => 'public-read',
                'ContentType' => 'image/jpeg'
            ));

            $s3->putObject(array(
                
                'Bucket' => AWS_BUCKET_SASP,
                'Key'    => $imgPrincipalName,
                'SourceFile'   => $_FILES['img_principal']['tmp_name'],
                'ACL'    => 'public-read',
                'ContentType' => 'image/jpeg'
            ));
        } 
        catch (Exception $e) { }
    
        if (!move_uploaded_file($_FILES['img_busca']['tmp_name'], $imgBuscaPath)) {
    
            $this->db->saspError("Erro\n\n" . $this->db->dbEscapeString(json_encode(error_get_last(), JSON_UNESCAPED_UNICODE)));
        }
    
        if (!move_uploaded_file($_FILES['img_principal']['tmp_name'], $imgPrincipalPath)) {
    
            $this->db->saspError("Erro\n\n" . $this->db->dbEscapeString(json_encode(error_get_last(), JSON_UNESCAPED_UNICODE)));
        }

        if ($modulo === 'pessoas') {

            $this->db->dbExecute("UPDATE tb_pessoas SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
            $this->db->dbExecute("UPDATE tb_pessoas_imagem SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        }
        else if ($modulo === 'abordagens') { //Demais modulos

            $this->db->dbExecute("UPDATE tb_abordagens SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
            $this->db->dbExecute("UPDATE tb_abordagens_imagem SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        }
        else if ($modulo === 'veiculos') {

            $this->db->dbExecute("UPDATE tb_veiculos_imagem SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        }
        else if ($modulo === 'informes') {

            $this->db->dbExecute("UPDATE tb_informes_imagem SET img_enviada = 1 WHERE img_busca = '{$imgBuscaName}' AND img_principal = '{$imgPrincipalName}' LIMIT 1");
        }
        else {

            $this->db->saspError('Módulo não encontrado.');
        }

        $this->db->saspSuccess('Imagens enviadas.');
    }
    else {

        $this->db->saspError('Acesso negado.');
    }

?>
