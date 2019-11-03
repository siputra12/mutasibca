<?php
@date_default_timezone_set('Asia/Jakarta');
class BCA{
	public function __construct($userid, $pwd, $date)
	{
		$this->_userId = $userid;
		$this->_password = $pwd;
		$this->_settingDate = $date;
		$this->_cookies = @file_get_contents("cookies_$userid.txt");
	}
	
	private function Timecustom($cmd)
	{
		$jarak = @explode("day", $cmd)[0];
		$time = time();
		$times = strtotime("-$jarak days", $time);
		$d = date('d', $time);
		$m = date('m', $time);
		$y = date('Y', $time);
		$ds = date('d', $times);
		$ms = date('m', $times);
		$ys = date('Y', $times);
		$dari = array("d" => $ds,"m" => $ms,"y" => $ys);
		$ke = array("d" => $d,"m" => $m,"y" => $y);
		if($type == null)
		{
			return array("dari" => $dari, "ke" => $ke);
		} else {
			return $$type;
		}
	}
	private function Timenow($time, $type = null)
	{
		$d = date('d', $time);
		$m = date('m', $time);
		$y = date('Y', $time);
		$dari = array("d" => $d,"m" => $m,"y" => $y);
		if($type == null)
		{
			return array("dari" => $dari, "ke" => $dari);
		} else {
			return $$type;
		}
	}
	private function getStr($a, $b, $c)
	{
		$a = @explode($a, $c)[1];
		$b = @explode($b, $a)[0];
		return $b;
	}
	
	private function mutasiSaldo($name)
	{
		$data = @str_replace('align="center">:</td>',"",$this->getData());
		$find = $this->getStr('align="left">'.$name.'</td>', '</td>', $data);
		$find = @explode('align="left">', $find)[1];
		return $find;
	}
	
	private function login()
	{
		$body = "value%28user_id%29=".$this->_userId."&value%28pswd%29=".$this->_password."&value%28Submit%29=LOGIN&value%28actions%29=login&value%28user_ip%29=&user_ip=&value%28mobile%29=true&value%28browser_info%29=&mobile=true";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://m.klikbca.com/authentication.do');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_POST, 1);
		$headers = array();
		$headers[] = 'Host: m.klikbca.com';
		$headers[] = 'Connection: close';
		$headers[] = 'Content-Length: '.strlen($body);
		$headers[] = 'Cache-Control: max-age=0';
		$headers[] = 'Origin: https://m.klikbca.com';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'Save-Data: on';
		$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 5.1.1; SM-G935FD) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.101 Safari/537.36';
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
		$headers[] = 'Referer: https://m.klikbca.com/login.jsp';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		if(strpos($result, "var err='")){
			return array("status" => "gagal", "msg" => $this->getStr("var err='","'",$result));
		}else{
			$cookie = "Cookie-NS-Mklikbca=".$this->getStr('Set-Cookie: Cookie-NS-Mklikbca=',';',$result)."; ";
			$cookie .= "JSESSIONID=".$this->getStr('Set-Cookie: JSESSIONID=',';',$result);
			@file_put_contents("cookies_".$this->_userId.".txt", $cookie);
			$this->_cookies = $cookie;
			return array("status" => "sukses", "cookies" => $cookie);
		}
	}
	
	private function getData(){
		Awal:
		if($this->_settingDate == "today"){
			$time = $this->Timenow(time());
			$timeDari = $time['dari'];
			$timeKe = $time['ke'];
		}else{
			$time = $this->Timecustom($this->_settingDate);
			$timeDari = $time['dari'];
			$timeKe = $time['ke'];
		}
		if(!file_exists("cookies_".$this->_userId.".txt"))
		{
			$login = $this->login();
			if($login['status'] == "gagal")
			{
				return array("status" => false, "data" => array(), "message" => $login['msg']);
			}
		}
		$body = "r1=1&value%28D1%29=0&value%28startDt%29={$timeDari['d']}&value%28startMt%29={$timeDari['m']}&value%28startYr%29={$timeDari['y']}&value%28endDt%29={$timeKe['d']}&value%28endMt%29={$timeKe['m']}&value%28endYr%29={$timeKe['y']}";
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://m.klikbca.com/accountstmt.do?value(actions)=acctstmtview');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Host: m.klikbca.com';
		$headers[] = 'Connection: close';
		$headers[] = 'Content-Length: '.strlen($body);
		$headers[] = 'Cache-Control: max-age=0';
		$headers[] = 'Origin: https://m.klikbca.com';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'Save-Data: on';
		$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 5.1.1; SM-G935FD) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.101 Safari/537.36';
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
		//$headers[] = 'Referer: https://m.klikbca.com/accountstmt.do?value(actions)=acct_stmt';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'Cookie: '.$this->_cookies;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		curl_close($ch);
		if(strpos($result, "302 Moved Temporarily"))
		{
			unlink("cookies_".$this->_userId.".txt");
			goto Awal;
		}else
		if(strpos($result, "TRANSAKSI GAGAL"))
		{
			return array("status" => false, "data" => array(), "message" => "Transaksi Gagal");
		}else
		if(strpos($result, "TIDAK ADA TRANSAKSI")){
			return array("status" => false, "data" => array(), "message" => "Tidak Ada Transaksi Pada Tanggal {$timeDari['d']}/{$timeDari['m']}/{$timeDari['y']}");
		}
		return $result;
	}
	
	public function mutasiTrx($date = null)
	{
		$d = date('d', time());
		$m = date('m', time());
		$arr = array("  ","<tr bgcolor='#e0e0e0'><td valign='top'>","<tr bgcolor='#f0f0f0'><td valign='top'>","	","</tr>","\r","\n\n","</td><td>SWITCHING DB      <br>","</td><td>SWITCHING CR      <br>");
		$arr2 = array("<td valign='top'>","<br>","\n");
		$res = $this->getData();
		$str = $this->getStr("<br>$m/$d ", " ", $res);
		$arr3 = array("PEND </td><td>","<br>$m/$d $str","  ");
		if(@$res['status']){
			$res = $this->getStr('<td bgcolor="#e0e0e0" colspan="2"><b>KETERANGAN</td>','<!--<tr>',$res);
			$res = @str_replace($arr3,"",@str_replace($arr, "", $res));
			$c = @explode("\n", $res);
			$array = array("SALDO AWAL","SALDO AKHIR","MUTASI KREDIT","MUTASI DEBET");
			$results['status'] = true;
			$result = array();
			for($i=0;$i<count($array);$i++){
				$name = $array[$i];
				$name_arr = @str_replace(" ", "_", strtolower($name));
				$results[$name_arr] = $this->mutasiSaldo($name);
			}
			for($a=0;$a<count($c);$a++){
				$res = @str_replace($arr2, "|", $c[$a]);
				$ress = @explode("|",@str_replace("</td>", "", $res));
				$arr = array(",",".00");
				$ss = @str_replace($arr,"",$ress[count($ress)-2]);
				$res1['desc1'] = $ress[0];
				$res1['desc2'] = $ress[1];
				$res1['vendor'] = $ress[2];
				$res1['amount'] = $ress[count($ress)-2];
				//$res1['type'] = $ress[count($ress)-1];
				$result[$ress[count($ress)-1]][$ss] = $res1;
			}
			$results['data'] = $result;
			return @json_encode($results);
		}else{
			return @json_encode($res);
		}
	}
	
}
