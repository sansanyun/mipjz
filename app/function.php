<?php

use JonnyW\PhantomJs\Client;

use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;

    function getOS() {

        $os_name=PHP_OS;

        if (strpos($os_name,"Linux") !== false) {

            $os_str="Linux";

        }else if (strpos($os_name,"WIN") !== false) {

            $os_str="Win";

        } else {

            $os_str="Mac";

        }

        return $os_str; 

    }

    function getRenderFile () {

        if (getOS() == 'Mac') {

            $file = ROOT_PATH . 'system/vendor/bin/macphantomjs';

        }
        if (getOS() == 'Win') {

            $file = ROOT_PATH . 'system/vendor/bin/phantomjs.exe';

        }

        if (getOS() == 'Linux') {

            $file = ROOT_PATH . 'system/vendor/bin/phantomjs';

        }

        return $file;

    }

    

    function getRenderHtml($url) {

        if (!$url) {

            return false;

        }

        $client = Client::getInstance();

        $client->getEngine()->setPath(getRenderFile());

        $client->isLazy();

        $request = $client->getMessageFactory()->createRequest();

        $response = $client->getMessageFactory()->createResponse();

//      $request->setHeaders(array('Referer' => 'https://www.baidu.com'));

        $request->setUrl($url);

        $request->setMethod('GET');

        $request->setTimeout(5000);

        $request->setDelay(5);

        $client->send($request, $response);

        $res = $response->getContent();

        if ($res) {

            $htmlHeader = '<!DOCTYPE html><html>';

            $htmlFooter = '</html>';

            return $htmlHeader.$res.$htmlFooter;

        } else {

            return '';

        }

    }

    function viewRender() {

        //  $location = ROOT_PATH . 'addons/test';

        //  $serviceContainer = ServiceContainer::getInstance();

        //  $procedureLoader = $serviceContainer->get('procedure_loader_factory')->createProcedureLoader($location);

        //  $client = Client::getInstance();

        //  $client->getEngine()->setPath(ROOT_PATH . 'system/vendor/bin/macphantomjs');

        //  $client->setProcedure('test11');

        //  $client->getProcedureLoader()->addLoader($procedureLoader);

        //  $request  = $client->getMessageFactory()->createRequest();

        //  $response = $client->getMessageFactory()->createResponse();

        //  $client->send($request, $response);

    }
    
	function encrypt_url($url,$key) {
        return base64_encode($url);
	}
	function decrypt_url($url,$key){
	    return base64_decode($url);
	}
	function geturl($str,$key){
	    $str = decrypt_url($str,$key);
	    return $str;
	}
	
	function isBaidu($ip)
	{
		if (!$ip) {
			return false;
		}
		
		$ipArray = [
			'14.215.177','27.221.36','27.221.37','27.221.38','27.221.39','27.221.40','36.110.198','36.248.6','42.81.93','42.236.4','42.236.7','58.20.204','58.215.118','58.215.123','58.216.2','58.217.200','58.217.202','59.38.112','59.51.81','59.53.69','60.28.22','61.54.47','61.135','61.155.149','61.155.165','61.182.137','61.233.141','63.243.252','101.69.162','101.71.56','103.235.44','103.235.45','103.235.46','103.235.47','104.193.88','104.193.89','106.12','106.13','106.120.159','111.1.52','111.7.168','111.12.25','111.13.100','111.13.113','111.20.242','111.32.132','111.47.212','111.62.0','111.177.3','111.202.114','111.206','112.25.86','112.65.203','112.80.248','112.80.252','112.80.255','112.84.34','112.253.12','113.105.148','113.113.73','113.215.19','115.231.42','115.239.210','115.239.211','115.239.212','116.31.127','116.211.117','117.27.148','117.27.149','117.27.232','117.34.28','117.34.37','117.34.112','117.157.16','117.161.5','117.169.99','117.174.144','118.123.116','118.123.210','118.192.48','119.63.192','119.63.193','119.63.194','119.63.195','119.63.196','119.63.197','119.63.198','119.63.199','119.75','119.146.74','119.147.134','119.167.246','120.52.29','120.52.114','120.204.206','120.241.70','121.32.89','121.32.89','122.193.41','122.228.234','123.125','123.138.46','124.95.170','124.192.164','124.193.227','124.238.238','125.39.78','125.39.79','150.138.138','150.242.123','153.3.236','157.255.71','159.226.50','162.105.207','163.177.8','163.177.151','180.76','180.97.33','180.97.34','180.97.35','180.97.36','180.97.104','180.149.131','180.149.132','180.149.133','180.149.144','180.149.145','182.61','182.118.47','182.150.1','183.60.131','183.131.34','183.230.68','185.10.104','185.10.105','185.10.106','185.10.107','202.46.48','202.46.49','202.46.50','202.46.51','202.46.52','202.46.53','202.46.54','202.46.55','202.46.56','202.46.57','202.46.58','202.46.59','202.46.60','202.46.61','202.46.62','202.108.22','202.108.23','202.108.249','202.108.250','211.90.25','211.97.81','211.144.71','218.17.55','220.113.150','220.181','221.180.244','221.195.34','221.204.160','222.35.78','222.199.144','222.199.188','222.199.189','222.199.190','222.199.191','222.216.190','222.216.229','223.95.34','223.99.240',
		];
		$status = false;
		foreach ($ipArray as $key => $value) {
			if (strpos($ip,$value) !== false) {
				$status = true;
				break;
			}
		}
		
		return $status;
	}

	function domain($param) {
        return config('domainStatic') . $param;
    }

    function domains($param) {
        return config('domains') . $param;
    }