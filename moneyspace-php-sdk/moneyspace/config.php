<?php

class Config {

    private $secret_id  = "";
	private $secret_key = "";
	////////////////////////////////////////////////////////////////////////////////////////

	public function getSecret_id() {
		return $this->secret_id;
	}
	public function getSecret_key() {
		return $this->secret_key;
	}

}
