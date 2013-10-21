<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 12:07 PM
 * To change this template use File | Settings | File Templates.
 */

class YiiElephantIOComponent extends CApplicationComponent {

	/**
	 * @var string path for elephant io library, should be in extensions directory
	 */
	public $elephantIOExtDir = 'elephant.io';

	/**
	 * @var string
	 */
	public $host;

	/**
	 * @var integer|string
	 */
	public $port;

	/**
	 * @var int in miliseconds
	 */
	public $handshakeTimeout = 3000;

	public function init() {
		parent::init();
		include_once Yii::getPathOfAlias('ext') . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
			$this->elephantIOExtDir,
			'lib',
			'ElephantIO',
			'Client.php'
		));
	}

	/**
	 * @param null $host
	 * @param null $port
	 *
	 * @return \ElephantIO\Client
	 */
	public function createClient($host = null, $port = null) {
		if (!isset($host)) {
			$host = $this->host;
		}
		if (!isset($port)) {
			$port = $this->port;
		}
		return new \ElephantIO\Client(
			sprintf('http://%s:%s', $host, $port),
			'socket.io',
			1,
			false
		);
	}

	/**
	 * @param string $path namespace on nodejs socket.io server
	 * @param string $event event name in current namespace
	 * @param mixed  $data event data
	 * @param mixed  $data,...
	 *
	 */
	public function emit($path, $event, $data) {
		$elephant = $this->createClient();
		$elephant->setHandshakeTimeout($this->handshakeTimeout);
		$elephant->init();
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		$elephant->createFrame()->endPoint($path)->emit($event, $args);
		$elephant->close();
	}
}