<?php

/**
*	
*	����� ��� ������ � ������ ������
*	
**/

class db {

	var $con;																																			// ������ �� �����������

/**
*	
*	����� ��� ������ � ������ ������
*	
**/
	public function db() {
	
		global $GAMINAS;
		
		$host = 'localhost';
		$user = 'root';
		$pw = '230105';
		$base = 'gaminas';
		
// ������������ � ����, ������������� ������, ����� ������ � ��������� ��������

		$link = mysqli_connect($host, $user, $pw, $base);
		
		if (!$link) {
			printf("<h2>���������� ������������ � ���� ������.</h2> ��� ������: %s\n", mysqli_connect_error());
			exit;
		} else $this->con = $link;
		
		if (!mysqli_set_charset($link, "utf8")) {
			$GAMINAS['backtrace'][] = "Error loading character set utf8: " . mysqli_error($link);
		} else {
			$GAMINAS['backtrace'][] = "Current character set: " . mysqli_character_set_name($link);
		}
	}
	
	public function query($query) {
		$query_result = mysqli_query($this->con, $query);
		fb(gettype($query_result));
		if (gettype($query_result) !== 'boolean') $res = mysqli_fetch_all($query_result);
		else $res = ($query_result === FALSE) ? 'FALSE: ' . mysqli_error($this->con) : $query_result;
		fb($res);
		if (mysqli_error($this->con) != '') return mysqli_error($this->con);
		return $res;
	}
}

?>