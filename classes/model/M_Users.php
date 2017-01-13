<?php

class M_Users
{	
	private static $instance;	// экземпляр класса
	private $mysql; 			// драйвер БД
	private $sid;				// идентификатор текущей сессии
	private $uid;				// идентификатор текущего пользователя

	// Получение единственного экземпляра (одиночка)
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct()
	{
		$this->mysql = M_MYSQL::getInstance();
		$this->sid = null;
		$this->uid = null;
	}

	public function registration($mail, $pass, $name)
	{
		$obj = ['login' => $mail, 'password' => md5($pass), 'id_role' => 1, 'name' => $name];
		$result = $this->mysql->insert('users', $obj);
        if(isset($result)) {
            $_SESSION['notice'] = 'Вы успешно зарегистрировались';
        }
	}

	//TODO сделать проверку введенных данных для регистрации
    public function check_reg($mail, $pass, $name)
    {

    }

	// Очистка неиспользуемых сессий
	public function clearSessions()
	{
		$min = date('Y-m-d H:i:s', time() - 60 * 20);
		$t = "time_last < '%s'";
		$where = sprintf($t, $min);
		$this->mysql->delete('sessions', $where);
	}

	// Авторизация
	// $login 		- логин
	// $password 	- пароль
	// $remember 	- нужно ли запомнить в куках
	// результат	- true или false
	public function login($login, $password, $remember = true)
	{
		// вытаскиваем пользователя из БД 
		$user = $this->getByLogin($login);

		if ($user == null)
			return false;
		
		$id_user = $user['id_user'];
				
		// проверяем пароль
		if ($user['password'] != md5($password))
			return false;
				
		// запоминаем имя и md5(пароль)
		if ($remember)
		{
			$expire = time() + 3600 * 24 * 100;
			setcookie('login', $login, $expire);
			setcookie('password', md5($password), $expire);
		}		
				
		// открываем сессию и запоминаем SID
		$this->sid = $this->openSession($id_user);
		
		return true;
	}

	// Выход
	public function logout()
	{
		setcookie('login', '', time() - 1);
		setcookie('password', '', time() - 1);
		unset($_COOKIE['login']);
		unset($_COOKIE['password']);
		unset($_SESSION['sid']);
		unset($_SESSION['name']);
		$this->sid = null;
		$this->uid = null;
	}

	// Получение пользователя
	// $id_user		- если не указан, брать текущего
	// результат	- объект пользователя
	public function get($id_user = null)
	{	
		// Если id_user не указан, берем его по текущей сессии.
		if ($id_user == null)
			$id_user = $this->getUid();
			
		if ($id_user == null)
			return null;
			
		// А теперь просто возвращаем пользователя по id_user.
		$t = "SELECT * FROM users WHERE id_user = '%d'";
		$query = sprintf($t, $id_user);
		$result = $this->mysql->selectOne($query);
		$_SESSION['name'] = $result['name'];
		return $result;
	}

	// Получает пользователя по логину
	public function getByLogin($login)
	{	
		$t = "SELECT * FROM users WHERE login = '%s'";
		$query = sprintf($t, $this->mysql->real_escape_string($login));
		$result = $this->mysql->select($query);
		return $result[0];
	}

    public function usersList()
    {
        $query = "SELECT `id_user`, `id_role`, `name` FROM `users`";
		$result = $this->mysql->select($query);
		return $result;
    }

	public function usersRole()
	{
		$query = "SELECT `id_role` FROM `users`";
		$result = $this->mysql->select($query);
		return $result;
	}

	public function usersRoleUpdate($array)
	{
		foreach ($array as $id => $role) {
			$object = ['id_role' => $role];
			$where = "`id_user` = '$id'";
			$this->mysql->update('users', $object, $where);
		}
	}

	// Проверка наличия привилегии
	// $priv 		- имя привилегии
	// $id_user		- если не указан, значит, для текущего
	// результат	- true или false
	//TODO переделать, придумать нормальный запрос
	public function can($priv, $id_user = null)
	{
		if($id_user == null) {
			$id_user = $this->getUid();
		}

		if($id_user == null) {
			return false;
		}

		$query1 = "SELECT `id_priv` FROM `privs` WHERE `name` = '$priv'";
		$result1 = $this->mysql->select($query1);
		$id_priv = $result1['0']['id_priv'];

		$query2 = "SELECT `id_role` FROM `users` WHERE `id_user` = '$id_user'";
		$result2 = $this->mysql->select($query2);
		$id_role = $result2['0']['id_role'];

		$query3 = "SELECT count(*) as count  FROM privs2roles WHERE id_priv = '$id_priv' AND id_role = '$id_role'";
		$result3 = $this->mysql->select($query3);

		if($result3['0']['count'] > 0) {
			return true;
		}
		return false;
	}

	// Проверка активности пользователя
	// $id_user		- идентификатор
	// результат	- true если online
	//TODO переделать, я похоже не так третье задание понял)
	public function isOnline()
	{
		$sql = "SELECT `name` FROM `users` WHERE `id_user` IN (SELECT DISTINCT `id_user` FROM `sessions`)";
		$result = $this->mysql->select($sql);

		foreach ($result as $value) {
			$res [] = $value['name'];
		}

		$res = implode(', ', $res);

		return $res;
	}

	// Получение id текущего пользователя
	// результат	- UID
	public function getUid()
	{	
		// Проверка кеша.
		if ($this->uid != null)
			return $this->uid;	

		// Берем по текущей сессии.
		$sid = $this->getSid();
				
		if ($sid == null)
			return null;
			
		$t = "SELECT id_user FROM sessions WHERE sid = '%s'";
		$query = sprintf($t, $this->mysql->real_escape_string($sid));
		$result = $this->mysql->select($query);
				
		// Если сессию не нашли - значит пользователь не авторизован.
		if (count($result) == 0)
			return null;
			
		// Если нашли - запоминм ее.
		$this->uid = $result[0]['id_user'];
		return $this->uid;
	}

	// Функция возвращает идентификатор текущей сессии
	// результат	- SID
	private function getSid()
	{
		// Проверка кеша.
		if ($this->sid != null)
			return $this->sid;
	
		// Ищем SID в сессии.
		$sid = $_SESSION['sid'];
								
		// Если нашли, попробуем обновить time_last в базе. 
		// Заодно и проверим, есть ли сессия там.
		if ($sid != null)
		{
			$session = array();
			$session['time_last'] = date('Y-m-d H:i:s'); 			
			$t = "sid = '%s'";
			$where = sprintf($t, $this->mysql->real_escape_string($sid));
			$affected_rows = $this->mysql->update('sessions', $session, $where);

			if ($affected_rows == 0)
			{
				$t = "SELECT count(*) FROM sessions WHERE sid = '%s'";		
				$query = sprintf($t, $this->mysql->real_escape_string($sid));
				$result = $this->mysql->select($query);
				
				if ($result[0]['count(*)'] == 0)
					$sid = null;			
			}			
		}		
		
		// Нет сессии? Ищем логин и md5(пароль) в куках.
		// Т.е. пробуем переподключиться.
		if ($sid == null && isset($_COOKIE['login']))
		{
			$user = $this->getByLogin($_COOKIE['login']);
			
			if ($user != null && $user['password'] == $_COOKIE['password'])
				$sid = $this->openSession($user['id_user']);
		}
		
		// Запоминаем в кеш.
		if ($sid != null)
			$this->sid = $sid;
		
		// Возвращаем, наконец, SID.
		return $sid;		
	}

	// Открытие новой сессии
	// результат	- SID
	private function openSession($id_user)
	{
		// генерируем SID
		$sid = $this->generateStr(10);
				
		// вставляем SID в БД
		$now = date('Y-m-d H:i:s'); 
		$session = array();
		$session['id_user'] = $id_user;
		$session['sid'] = $sid;
		$session['time_start'] = $now;
		$session['time_last'] = $now;				
		$this->mysql->insert('sessions', $session);
				
		// регистрируем сессию в PHP сессии
		$_SESSION['sid'] = $sid;				
				
		// возвращаем SID
		return $sid;	
	}


	// Генерация случайной последовательности
	// $length 		- ее длина
	// результат	- случайная строка
	private function generateStr($length = 10)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  

		while (strlen($code) < $length) 
            $code .= $chars[mt_rand(0, $clen)];  

		return $code;
	}
}