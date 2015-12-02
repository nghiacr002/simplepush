<?php
	namespace SimplePush;
	class IOS extends \SimplePush\Provider{
		
		const APNS_PUSH_SANBOX_POINT = "ssl://gateway.sandbox.push.apple.com";
		const APNS_PUSH_POINT = "ssl://gateway.push.apple.com";
		const APNS_PUSH_PORT = "2195";
		
		const APNS_FEEDBACK_POINT = "ssl://feedback.push.apple.com";
		const APNS_FEEDBACK_SANDBOX_POINT = "ssl://feedback.sandbox.push.apple.com";
		const APNS_FEEDBACK_PORT = "2196";
		
		private $sCertifcatedFile;
		private $iConnectTimeOut = 60;
		private $sPassPhrase = "";
		public function __construct($aConfigs)
		{
			$this->sName = "APNS";
			$this->sVersion = "1.0";
			parent::__construct($aConfigs);
			$this->sCertifcatedFile = isset($aConfigs['certification_file'])?$aConfigs['certification_file']:"";
			$this->sPassPhrase = isset($aConfigs['password'])?$aConfigs['password']:"";
		}
		public function ceritificate($sFileName)
		{
			$this->sCertifcatedFile = $sFile;
			return $this;
		}
		public function password($sPassPhrase)
		{
			$this->sPassPhrase = $sPassPhrase;
			return $this;
		}
		public function timeout($iTime)
		{
			$this->iConnectTimeOut = $iTime;
			return $this;
		}
		public function clean()
		{
			parent::clean();
			//$this->sCertifcatedFile = "";
			//$this->iConnectTimeOut = 60;
			//$this->sPassPhrase = "";
			return $this;
		}
		public function send()
		{
			if(!$this->validate())
			{
				return false;
			}
			try {
				$ctx = stream_context_create();
				//d($this->sCertifcatedFile);
				stream_context_set_option($ctx, 'ssl', 'local_cert', $this->sCertifcatedFile);
				if(!empty($this->sPassPhrase))
				{
					stream_context_set_option($ctx, 'ssl', 'passphrase', $this->sPassPhrase);
				}
				
				$sConnectPoint = ($this->bDevelopmentMode == true)? self::APNS_PUSH_SANBOX_POINT : self::APNS_PUSH_POINT;
				$sConnectPoint = $sConnectPoint.':'. self::APNS_PUSH_PORT;
				
				$fp = stream_socket_client($sConnectPoint, $iErrorCode, $sErrorReturn, $this->iConnectTimeOut, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if(!$fp)
				{
					$this->setError("Failed to connect to APNS: {$iErrorCode} {$sErrorReturn}");
				}
				else
				{
					$message = $this->aMesages['message'];
					
					if(is_array($message))
					{
						$message = json_encode($message);
					}
					$token = trim($this->aMesages['target_id']);	
					$msg = chr(0).pack("n",32).pack('H*',$token).pack("n",strlen($message)).$message;
					$this->mResults = $result = fwrite($fp, $msg, strlen($msg));
					if(!$result)
					{
						$this->setError("Failed writing to stream");
					}
					
				}
				fclose($fp);
			}catch(\Exception $ex)
			{
				$this->setError('['.$ex->getCode().']'. $ex->getMessage());
			}
			$this->postBack();
			$this->clean();
		}
		
		public function validate()
		{
			if(empty($this->sCertifcatedFile))
			{
				$this->aErrors[] = "Ceritication file cannot be empty";
				return false;
			}
			return parent::validate();
		}
		
	}