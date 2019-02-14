<?php

	class DataBase {

		var $link;

		function __construct() {

			$this->link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('{"error":"1","msg":"MySQL connecion fail.","extra":null}');

			mysqli_set_charset($this->link, DB_CHARSET);
		}

		function dbDie($string = null) {

			mysqli_close($this->link);

			die($string);
		}

		function dbEscapeString($string) {

			return 	mysqli_real_escape_string($this->link, $string);
		}

		function dbExecute($query) {

			$result = mysqli_query($this->link, $query . ';') or $this->dbDie('{"error":"1","msg":"' . mysqli_error($this->link) .'","extra":null}');

			return $result;
		}

		function dbRead($query) {

			$result = $this->dbExecute($query);

			if (!mysqli_num_rows($result)) {

				return false;
			}
			else {

				while ($res = mysqli_fetch_assoc($result)) {

					$data[] = $res;
				}

				return $data;
			}
		}

		function dbInsert($table, array &$data) {

			$fields = implode(', ', array_keys($data));
			$values = "'". implode("', '", $data) . "'";

			$query = "INSERT INTO {$table} ( {$fields} ) VALUES ( {$values} )";

			return $this->dbExecute($query);
		}

		function saspSuccess($msg, array &$extra = null) {

            header('Content-Type: text/plain; charset=utf-8');

            $response = array('error' => '0', 'msg' => $msg, 'extra' => $extra);

            $this->dbDie(json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        function saspError($msg, array &$extra = null) {

			$usuario = 0;

			if (!empty($_POST['cpf'])) {

				$usuario = $_POST['cpf'];
			}

			$post_content = json_encode($_POST, JSON_UNESCAPED_UNICODE);

			$erro = array(

				'cpf'			=> $usuario,
				'erro'			=> $msg,
				'post_content'	=> $post_content
			);

			$this->dbInsert('tb_sistema_erro', $erro);

            header('Content-Type: text/plain; charset=utf-8');

            $response = array('error' => '1', 'msg' => $msg, 'extra' => $extra);

            $this->dbDie(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
	}

?>
