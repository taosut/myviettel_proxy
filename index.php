<?php
  define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
  require_once ROOT . 'vendor/autoload.php';

  function login($username, $password, $actionForm='adsl', $retries=0) {
    if ($retries >= 5) return 'Error';

    $data = [
      'actionForm' => $actionForm,
      'appID' => 'id1014838705',
      'build_code' => '2019.1.25.1',
      'device_id' => 'C58D8EA3-745A-4E4C-843C-E91D23205EE8',
      'device_name' => 'iPhone10,6',
      'os_type' => 'ios',
      'password' => $password,
      'username' => $username,
      'version_app' => '3.13.2',
    ];

    $headers = [
      'User-Agent'=> 'My Viettel/3.13.2 (iPhone; iOS 12.1.3; Scale/3.00)',
      'Accept-Language'=> 'en-VN;q=1'
    ];

    try {
      $response = Requests::post('https://apivtp.vietteltelecom.vn:6768/myviettel.php/loginV2', $headers, $data, [
          'verify'=> false,
          'timeout'=> 3
          // 'proxy'=> 'localhost:8888'
        ]);

      return $response->body;
    } catch (Exception $e) {
      return login($username, $password, $actionForm='adsl', $retries++);
    }
  }

  function get_captcha($retries) {
    if ($retries >= 5) return 'Error';

    try {
      $response = Requests::get('https://apivtp.vietteltelecom.vn:6768/myviettel.php/getCaptcha', [], [
        'verify' => false,
        'timeout' => 3
      ]);

      return $response->body;
    } catch (Exception $e) {
      return get_captcha($retries++);
    }
  }

  function pay($phone, $captcha, $cardcode, $sid, $token) {
    if ($retries >= 5) return 'Error';

    $data = [
      'build_code'=>  '2019.1.25.1',
      'captcha'=> $captcha,
      'cardcode'=>  $cardcode,
      'device_name'=> 'iPhone10,6',
      'os_type'=> 'ios',
      'phone'=> $phone,
      'sid'=> $sid,
      'token'=> $token,
      'type'=>  '1',
      'version_app'=> '3.13.2',
    ];

    try {
      $response = Requests::post('https://apivtp.vietteltelecom.vn:6768/myviettel.php/paymentOnlineV2', [], [
        'verify' => false,
        'timeout' => 3
      ]);

      return $response->body;
    } catch (Exception $e) {
      return get_captcha($retries++);
    }
  }

  if (isset($_GET['function'])) {
    $function = $_GET['function'];

    if ($function === 'login') {
      if (isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $actionForm = 'adsl';
        if (isset($_GET['actionForm'])) $actionForm = $_GET['actionForm'];

        echo login($username, $password, $actionForm);
      } else die();
    } else if ($function === 'get_captcha') {
      echo get_captcha();
    } else if ($function === 'pay') {
      if (isset($_GET['phone']) && isset($_GET['captcha']) && isset($_GET['cardcode']) && isset($_GET['sid']) && isset($_GET['token'])) {
        $phone = $_GET['phone'];
        $captcha = $_GET['captcha'];
        $cardcode = $_GET['cardcode'];
        $sid = $_GET['sid'];
        $token = $_GET['token'];

        echo pay($phone, $captcha, $cardcode, $sid, $token);
      } else die();
    }
  }