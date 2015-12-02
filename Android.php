<?php
	namespace SimplePush;
	class Android extends \SimplePush\Provider{
		const GCM_POINT_HTTP = "https://gcm-http.googleapis.com/gcm/send";
		//const GCM_POINT_XMPP = "gcm-xmpp.googleapis.com:5235";
		private $API_ACCESS_KEY;
		private $sTopicId; 
		private $aOptions;
		private $bIsXMPP;
		public function __construct($aConfigs)
		{
			$this->sName = "Android GCM";
			$this->sVersion = "1.0";
			parent::__construct($aConfigs);
			$this->API_ACCESS_KEY = isset($aConfigs['api_access_key'])?$aConfigs['api_access_key']:"";
			$this->aOptions = array();
			$this->bIsXMPP = false;
		}
		public function topic($sTopicId)
		{
			$this->sTopicId = $sTopicId;
			return $this;
		}
		public function xmpp($bValue = true)
		{
			$this->bIsXMPP = $bValue;
			return $this;
		}
		public function options($aOptions)
		{
			$this->aOptions = $aOptions;
			return $this;
		}
		public function clean()
		{
			parent::clean();
			$this->sTopicId = "";
			$this->aOptions = array();
		}
		public function send()
		{
			if(!$this->validate())
			{
				return false;
			}
			$mResult = "";
			try{
				$sData = isset($this->aMesages['message'])?$this->aMesages['message']:"";
				$aFields = array(
					'data' => $sData
				);
				if(!empty($this->sTopicId))
				{
					$aFields['to'] = $this->sTopicId;					
				}
				else
				{
					$aIds = isset($this->aMesages['target_id'])?$this->aMesages['target_id']:array();
					$aFields['registration_ids'] = $aIds;
					if(!is_array($aFields['registration_ids']))
					{
						$aFields['registration_ids']= array($aFields['registration_ids']);
					}
				}
				if(is_array($this->aOptions) && count($this->aOptions))
				{
					$aFields = array_merge($aFields, $this->aOptions);
				}
				$aHeader = array(
					'Authorization: key=' . $this->API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				$mResult = $this->post(Android::GCM_POINT_HTTP, $aFields,$aHeader);
				$mResult = json_decode($mResult,true);
				$this->mResults = $mResult;
				if(isset($mResult['failure']) && $mResult['failure'] > 0)
				{
					$this->aErrors = $mResult['results']['error'];
				}
			}catch(Exception $ex)
			{
				$this->setError('['.$ex->getCode().']'. $ex->getMessage());
			}
			$this->postBack();
			$this->clean();
		}
		public function validate()
		{
			if(empty($this->sTopicId))
			{
				return parent::validate();
			}
			if(!isset($this->aMesages['message']) || empty($this->aMesages['message']))
			{
				$this->aErrors[] = "Message Body cannot be empty";
				return false;
			}
			return true;
		}
	}
?>