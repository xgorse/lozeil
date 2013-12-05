<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class User extends Record  {
	public $id = 0;
	public $name = "";
	public $username = "";
	public $password = "";
	public $email = "";

	protected $db = null;

	function __construct($user_id = 0, $db = null) {
		$this->id = (int)$user_id;

		if ($db === null) {
			$db = new db();
		}

		$this->db = $db;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load(array $key = array(), $table = "users", $columns = null) {
		if (empty($key) or $key->id == 0) {
			if ($this->id === 0) {
				return false;
			} else {
				$key = array ("id" => $this->id);
			}
		}
		return parent::load($key, $table, $columns);
	}
	
	function save() {
		if (is_numeric($this->id) and $this->id != 0) {
			$this->id = $this->update();

		} else {
			$this->id = $this->insert();
		}

		return $this->id;
	}
	
	function insert() {
		$query = "INSERT INTO ".$this->db->config['table_users']."
			SET name = ".$this->db->quote($this->name).",
			username = ".$this->db->quote($this->username).", ";
			if (isset($this->password) and !empty($this->password)) {
				$query .= " password = ".$GLOBALS['config']['mysql_password']."(".$this->db->quote($this->password)."), ";
			}
			$query .=" email = ".$this->db->quote($this->email);
				
		$result = $this->db->id($query);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('user'));

		return $this->id;
	}
	
	function update() {
		$query = "UPDATE ".$this->db->config['table_users'].
		" SET name = ".$this->db->quote($this->name).",
		username = ".$this->db->quote($this->username).", ";
		if (isset($this->password) and !empty($this->password)) {
			$query .= " password = ".$GLOBALS['config']['mysql_password']."(".$this->db->quote($this->password)."), ";
		}
		$query .=" email = ".$this->db->quote($this->email)."
		WHERE id = ".(int)$this->id;
		
		$result = $this->db->query($query);
		
		$this->db->status($result[1], "u", __('user'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_users'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "d", __('user'));

		return $this->id;
	}
	
	function password_request() {
		if ($this->email) {
			$db = new db();

			$time = time();
			$token = md5($time.$this->username.$this->password);
			$id = $db->id("
				INSERT INTO ".$db->config['table_passwordrequests']."
				(user_id, timestamp, token, completed)
				VALUES (".(int)$this->id.", ".(int)$time.", ".$db->quote($token).", 0)"
			);

			$url = $GLOBALS['config']['root_url']."/index.php?content=passwordrequest.php&token=".$token;

			$emails = array(
				array(
					'To' => $this->email,
					'ToName' => $this->name,
					'From' => $GLOBALS['param']['email_from'],
					'FromName' => $GLOBALS['config']['name'],
					'Subject' => sprintf($GLOBALS['array_email']['password_request'][0], $this->username),
					'Body' => sprintf($GLOBALS['array_email']['password_request'][1], $this->username, $url),
				),
			);
			
			email_send($emails);

			return true;
		}

		return false;
	}
	
	function password_reset($token) {
		$db = new db();
		$result = $db->query("
			SELECT id, user_id, timestamp
			FROM ".$db->config['table_passwordrequests']."
			WHERE token = ".$db->quote($token)."
			AND completed = 0
			LIMIT 0, 1"
		);

		if ($result[1]) {
			$row = $db->fetchArray($result[0]);

			if ($row['timestamp'] > strtotime("-1 hour")) {
				$this->load(array('id' => $row['user_id']));
				$this->password = substr(md5(time().$token), 0, 8);
				$this->save();
				$db->query("
					UPDATE ".$db->config['table_passwordrequests']."
					SET completed = 1
					WHERE id = ".(int)$row['id']
				);

				$emails = array(
					array(
						'To' => $this->email,
						'ToName' => $this->name,
						'From' => $GLOBALS['param']['email_from'],
						'FromName' => $GLOBALS['config']['name'],
						'Subject' => sprintf($GLOBALS['array_email']['new_password'][0], $this->username),
						'Body' => sprintf($GLOBALS['array_email']['new_password'][1], $this->username, $this->password),
					),
				);

				email_send($emails);

				return true;
			}
		}

		return false;
	}
	
	function defaultpage($context = "") {
		if (empty($this->defaultpage)) {
			$this->defaultpage = "writings.php";
		}

		return $this->defaultpage;
	}
}
