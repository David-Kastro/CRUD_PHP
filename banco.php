<?php

/**
* CRUD Básico
*
* Uma classe que conecta a um banco de dados MySQL atraves do construtor
*e executa as funçoes basicas de um CRUD.
*
*@author Davi Duarte <daviduartedf@gmail.com>
*@version 1.0
*
*/

class Banco{
	private $pdo;
	private $numRows;
	private $array;

	public function __construct($host, $dbname, $dbuser, $dbpass){
		try{
			$this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host, $dbuser, $dbpass);

		}catch(PDOException $e){
			echo "ERROR: ".$e->getMessage();
		}
	}

	public function query($sql){
		$query = $this->pdo->query(addslashes($sql));      //Executa a Query
		$this->numRows = $query->rowCount();              //Pega o Numero de Resultados (em $numRows)
		$this->array = $query->fetchAll();               //Cria um Array com todos os resultados (em $array)
	}

	public function result(){
		return $this->array;   //Retorna os resultados (em $array)
	}

	public function numRows(){
		return $this->numRows;  //Retorna o numero de resultados (em $numRows)
	}

	public function insert($tb_name, $values){

		//Verifica se os dois parametros nao estao vazios e se o segundo parametro é um Array
		if(!empty($tb_name) && is_array($values) && !empty($values)){
			$colunas = array();
			$dados = array();

			// Separa os nomes das colunas e o valor das colunas em arrays diferentes ($colunas e $dados) para fazer a montagem da query (em $sql)
			foreach ($values as $coluna => $dado) {
				$colunas[] = addslashes($coluna);
				$dados[] = "'".addslashes($dado)."'";
			}
			$sql = "INSERT INTO ".$tb_name." (".implode(", ", $colunas).") VALUES (".implode(", ", $dados).");";  //Query final
			
			$this->pdo->query($sql);   // Executa a Query e insere os valores na tabela
		}
	}

	//O usuario tem a opçao de nao especificar onde ira ocorrer a altereçao ("WHERE") ou a condiçao da query ("OR", "AND" e etc..)
	public function update($tb_name, $values, $where = array(), $where_cond = "AND"){   //Caso o usuario nao especifique, a funçao ira usar como padrao "array()" e "AND" para $where e $where_cond
		
		if(!empty($tb_name) && !empty($values) && is_array($values) && is_array($where)){
			//Monta a parte da query que muda o valor de uma ou mais colunas
			$dados = array();
			$row_ref = array();
			foreach ($values as $coluna => $dado) {
				$dados[] = addslashes($coluna)." = '".addslashes($dado)."'";
			}
			$sql = "UPDATE ".$tb_name." SET ".implode(", ", $dados);
			//Monta a parte da query que indica onde sera feita a mudança
			if(!empty($where)){
				$dados = array();
				foreach ($where as $coluna => $dado) {
					$dados[] = addslashes($coluna)." = '".addslashes($dado)."'";
				}
				$sql = $sql." WHERE ".implode(" ".$where_cond." ", $dados);  //Query final
			}

			$this->pdo->query($sql);  //Executa a Query e atualiza a tabala nas linhas especificadas (ou em todas)

		}
	}

	public function delete($tb_name, $where, $where_cond = "AND"){

		if(!empty($tb_name) && !empty($where) && is_array($where)){
			//Monta a parte da query que indica os valores que a linha a ser deletada deve possuir
			$dados = array();
			foreach ($where as $coluna => $dado) {
				$dados[] = addslashes($coluna)." = '".addslashes($dado)."'";
			}
			$sql = "DELETE FROM ".$tb_name." WHERE ".implode(" ".$where_cond." ", $dados); //Query final

			$this->pdo->query($sql); //Executa a Query e deleta as linhas que contem os valores indicados
		}
	}
}
?>



