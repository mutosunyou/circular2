<?php
/**
 * Research Artisan Lite: Website Access Analyzer
 * Copyright (C) 2009 Research Artisan Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * @copyright Copyright (C) 2009 Research Artisan Project
 * @license GNU General Public License (see license.txt)
 * @author ossi
 */
class Mailer {

  const NEWLINE = "\r\n";
  const END_DATA = '.';
  const SOCKET_TIMEOUT = 10;
  const DEFAULT_SERVER = 'localhost';
  const DEFAULT_PORT = '25';
  const POP3_PORT = '110';
  const STARTTLS = 'STARTTLS';

  const CONNECT_ERR_CODE = -9910;
  const TIMEOUT_ERR_CODE = -9911;
  const COMMAND_FAILED_ERR_CODE = -9912;
  const AUTH_FAILED_ERR_CODE = -9913;
  const CONNECT_ERR_MSG = 'SMTP Cannot Connect Host => ';
  const TIMEOUT_ERR_MSG = 'SMTP Send Command Timeout';
  const COMMAND_FAILED_ERR_MSG = 'SMTP Send Command Failed => ';
  const AUTH_FAILED_ERR_MSG = 'SMTP AUTH Failed => ';

  private static $_enable_auth_types = array('CRAM-MD5', 'LOGIN', 'PLAIN');

  private $_socket = null;
  private $_server = self::DEFAULT_SERVER;
  private $_port = self::DEFAULT_PORT;
  private $_userid = null;
  private $_pswd = null;
  private $_auth_types = array();

  public function __construct($server=null, $port=null, $userid=null, $pswd=null) {
    RaLog::setCharset('JIS');
    if (!is_null($server)) $this->_server = $server;
    if (!is_null($port)) $this->_port = $port;
    if (!is_null($userid) && trim($userid) != '') $this->_userid = $userid;
    if (!is_null($pswd) && trim($pswd) != '') $this->_pswd = $pswd;
  }

  public function send($from, $to, $subject, $body) {
    $rtn = true;
	mb_language('ja');
	mb_internal_encoding(RaConfig::CHARSET);
    $server = $this->_server;
    $port = $this->_port;
    $header = $this->_makeHeader($from, $to, $subject);
    $body = $this->_makeBody($body);
    try {
      $this->_connect($server, $port);
      $this->_helo();
      $this->_smtpAuth();
      $this->_sendData($from, $to, $header, $body);
      $this->_quit();
      $this->_disConnect();
    } catch (Exception $exception) {
      $rtn = false;
    }
    return $rtn;
  }

  private function _connect($server, $port) {
    $socket = fsockopen($server, $port, $errno, $errstr, self::SOCKET_TIMEOUT);
    $reply = fgets($socket);
    stream_set_timeout($socket, self::SOCKET_TIMEOUT);
    $this->_socket = $socket;
    RaLog::write('CONNECT:'. $server. ':'. $port);
  }

  private function _disConnect() {
    $socket = $this->_socket;
    if (!is_null($socket)) fclose($socket);
    RaLog::write('DISCONNECT');
  }

  private function _helo() {
    $server = $this->_server;
    $heloReply = $this->_sendCommand('EHLO', $server);
    if ($heloReply === false || $this->_checkStatus($heloReply, '250') === false) $heloReply = $this->_sendCommand('HELO', $server);
    if ($this->_checkStatus($heloReply, '250') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' HELO => '. $heloReply, self::COMMAND_FAILED_ERR_CODE, true);
  }

  private function _smtpAuth() {
    $userid = $this->_userid;
    $pswd = $this->_pswd;
    $rtn = true;
    $auth_types = $this->_auth_types;
    if (count($auth_types) > 0) {
      foreach ($auth_types as $value) {
        $function = '_auth'. ucfirst(str_replace('-', '', $value));
        $rtn = $this->$function($userid, $pswd);
        if ($rtn === true) break; 
      }
    } else {
      if (!is_null($userid) && !is_null($pswd)) $rtn = $this->_popBeforeSmtp($userid, $pswd);
    }
    if ($rtn === false) throw new RaException(self::AUTH_FAILED_ERR_MSG. $userid. ':'. $pswd, self::AUTH_FAILED_ERR_CODE, true);
  }

  private function _sendData($from, $to, $header, $body) {
    $toEmails = $this->_getArray($to);
    //MAIL
    $reply = $this->_sendCommand('MAIL', 'FROM:<'. $from. '>');
    if ($this->_checkStatus($reply, '250') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' MAIL FROM => '. $reply, self::COMMAND_FAILED_ERR_CODE, true);
    //RCPT
    foreach ($toEmails as $toEmail) {
      $reply = $this->_sendCommand('RCPT', 'TO:<'. $toEmail. '>');
      if ($this->_checkStatus($reply, '250') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' RCPT TO => '. $reply, self::COMMAND_FAILED_ERR_CODE, true);
    }
    //DATA
    $reply = $this->_sendCommand('DATA');
    if ($this->_checkStatus($reply, '354') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' DATA => '. $reply, self::COMMAND_FAILED_ERR_CODE, true);
    $reply = $this->_sendCommand($this->_escapeNewline($header. $body). self::END_DATA);
    if ($this->_checkStatus($reply, '250') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' DATA(header & body) => '. $reply, self::COMMAND_FAILED_ERR_CODE, true);
  }

  private function _makeHeader($from, $to, $subject) {
	$subject = RaUtil::convertEncoding($subject, RaConfig::CHARSET, 'JIS');
    $header = '';
    $header .= 'MIME-Version: 1.0'. self::NEWLINE;
    $header .= 'Sender: '. $from. self::NEWLINE;
    $header .= 'X-Mailer: '. RaConfig::RA_NAME. self::NEWLINE;
    $header .= 'Date: '. date('r'). self::NEWLINE;
    $header .= 'Subject: '. $subject. self::NEWLINE;
    $header .= 'From: '. $from. self::NEWLINE;
    $header .= 'To: '. $to. self::NEWLINE;
    $header .= 'Content-Type: text/plain; charset=ISO-2022-JP'. self::NEWLINE;
    $header .= 'Content-Transfer-Encoding: 7bit'. self::NEWLINE;
    return $header;
  }

  private function _makeBody($body) {
	$body = RaUtil::convertEncoding($body, RaConfig::CHARSET, 'JIS'). self::NEWLINE;
    return $body;
  }

  private function _quit() {
    $server = $this->_server;
    $reply = $this->_sendCommand('QUIT');
    if ($this->_checkStatus($reply, '221') === false) throw new RaException(self::COMMAND_FAILED_ERR_MSG. ' QUIT => '. $reply, self::COMMAND_FAILED_ERR_CODE, true);
  }

  private function _authPlain($userid, $pswd) {
    $command = 'AUTH PLAIN';
    $reply = $this->_sendCommand($command. ' '. base64_encode($userid. "\0". $userid. "\0". $pswd));
    if ($this->_checkStatus($reply, '235') === false) $reply = $this->_sendCommand($command. ' '. base64_encode($userid. "\0". $pswd));
    if ($this->_checkStatus($reply, '235') === false) {
      $reply = $this->_sendCommand($command);
      if ($this->_checkStatus($reply, '334') === false) return false;
      $reply = $this->_sendCommand(base64_encode($userid. "\0". $userid. "\0". $pswd));
      if ($this->_checkStatus($reply, '235') === false) $reply = $this->_sendCommand(base64_encode($userid. "\0". $pswd));
      if ($this->_checkStatus($reply, '235') === false) return false;
    }
    return true;
  }

  private function _authLogin($userid, $pswd) {
    $command = 'AUTH LOGIN';
    $reply = $this->_sendCommand($command);
    if ($this->_checkStatus($reply, '334') === false) return false;
    $reply = $this->_sendCommand(base64_encode($userid));
    if ($this->_checkStatus($reply, '334') === false) return false;
    $reply = $this->_sendCommand(base64_encode($pswd));
    if ($this->_checkStatus($reply, '235') === false) return false;
    return true;
  }

  private function _authCrammd5($userid, $pswd) {
    $command = 'AUTH CRAM-MD5';
    $reply = $this->_sendCommand($command);
    if ($this->_checkStatus($reply, '334') === false) return false;
    $challenge = base64_decode(preg_replace('/^334\s/', '', $reply));
    $command = $userid. ' '. $this->_hmacMd5($pswd, $challenge);
    $reply = $this->_sendCommand(base64_encode($command));
    if ($this->_checkStatus($reply, '235') === false) return false;
    return true;
  }

  private function _authStarttls($userid, $pswd) {
    $socket = $this->_socket;
    $command = 'STARTTLS';
    $reply = $this->_sendCommand($command);
    if ($this->_checkStatus($reply, '220') === false) return false;
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    $this->_auth_types = array();
    $this->_helo();
    $this->_smtpAuth();
    return true;
  }

  private function _popBeforeSmtp($userid, $pswd) {
    $rtn = true;
    $server = $this->_server;
    $port = $this->_port;
    try {
      $this->_disConnect();
      $this->_connect($server, self::POP3_PORT);
      if ($this->_pop3($userid, $pswd) === false) throw new RaException(self::AUTH_FAILED_ERR_MSG. $userid. ':'. $pswd, self::AUTH_FAILED_ERR_CODE, true);
      $this->_disConnect();
      $this->_connect($server, $port);
      $this->_helo();
    } catch (Exception $exception) {
      $rtn = false;
    }
    return $rtn;
  }

  private function _pop3($userid, $pswd) {
    $command = 'USER '. $userid;
    $reply = $this->_sendCommand($command);
    if ($this->_checkStatus($reply, '+OK') === false) return false;
    $command = 'PASS '. $pswd;
    $reply = $this->_sendCommand($command);
    if ($this->_checkStatus($reply, '+OK') === false) return false;
    return true;
  }

  private function _sendCommand($command, $param=null) {
    $socket = $this->_socket;
    $auth_types = $this->_auth_types;
    if (!is_null($param)) $command = $command. ' '. $param;
    $reply = false;
    $rtn = fwrite($socket, $command. self::NEWLINE);
    RaLog::write('COMMAND:'. $command);
    while (true) {
      $reply = fgets($socket);
      RaLog::write('REPLY:'. $reply);
      if ($reply === false) {
        $metaData = stream_get_meta_data($socket);
        if ($metaData['timed_out']) throw new RaException(self::TIMEOUT_ERR_MSG, self::TIMEOUT_ERR_CODE, true);
        break;
      }
      if (preg_match('/^EHLO\s/', $command) || preg_match('/^HELO\s/', $command)) {
        if (count($auth_types) == 0) if (preg_match('/'. self::STARTTLS. '/', $reply)) array_push($auth_types, self::STARTTLS);
        if (count($auth_types) == 0) {
          foreach (self::$_enable_auth_types as $value) {
            if (preg_match('/'. preg_quote($value). '/', $reply)) array_push($auth_types, $value);
          }
        }
      }
      if (substr($reply, 3, 1) == ' ') break; 
    }
    $this->_auth_types = $auth_types;
    return $reply;
  }

  private function _checkStatus($reply, $code) {
    if ($reply === false) return false;
    $codes = $this->_getArray($code);
    $exp = '';
    foreach ($codes as $key => $value) {
      if ($key == 0) $exp .= '(';
      $exp .= preg_quote($value);
      if ($key == count($codes)-1) {
        $exp .= ')';
      } else {
        $exp .= '|';
      }
    }
    return preg_match('/^'. $exp. '\s/', $reply) ? true : false;
  }

  private function _getArray($param) {
    $values = array();
    if (strpos($param, ',') !== false) {
      $params = explode(',', $param);
      if (is_array($params)) {
        foreach ($params as $value) {
          array_push($values, trim($value));
        }
      }
    } else {
      array_push($values, trim($param));
    }
    return $values;
  }

  private function _hmacMd5($key, $data) {
    /**
     * see =>
     *  http://www.ipa.go.jp/security/rfc/RFC2104JA.html
     */
    $b = 64;
    $k = $key;
    $ipad = str_repeat(chr(0x36), $b);
    $opad = str_repeat(chr(0x5C), $b);
    if (strlen($k) > $b) $k = pack('H*', md5($k));
    if (strlen($k) < $b) $k = str_pad($k, $b, chr(0x00));
    $i_xor = $k ^ $ipad;
    $inner  = pack('H*', md5($i_xor. $data));
    $o_xor = $k ^ $opad;
    return md5($o_xor. $inner);
  }

  private function _escapeNewline($data) {
    return preg_replace('/\r\n|\r|\n/', self::NEWLINE, $data);
  }

}
?>
