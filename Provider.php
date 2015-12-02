<?php
    namespace SimplePush;
    abstract class Provider {
    	protected $sVersion; 
    	protected $sName;
    	static $sBasePath;
    	protected $aConfigs;
    	protected $aMesages;
    	protected $aErrors;
    	protected $sCallBackPoint; 
    	protected $bDevelopmentMode;
    	protected $mResults;
    	public function development($bValue)
    	{
    		$this->bDevelopmentMode = $bValue;
    		return $this;
    	}
    	public function getName()
    	{
    		return $this->sName;
    	}
    	public function getVersion()
    	{
    		return $this->sVersion;
    	}
    	public function getBasePath()
    	{
    		if(!self::$sBasePath)
    		{
    			self::$sBasePath = dirname(__FILE__);
    		}
    		return self::$sBasePath;
    	}
        public function __construct($aConfigs)
        {
        	$this->aConfigs = $aConfigs;
        	
        	$this->clean();
        }
        public function from($mValue)
        { 
        	$this->aMesages['from'] = $mValue;
        	return $this;
        }
        public function fromName($sName)
        {
        	$this->aMesages['from_name'] = $sName;
        	return $this;
        }
        public function to($sTargeId)
        {
        	$this->aMesages['target_id'] = $sTargeId;
        	return $this;
        }
        public function toName($sName)
        {
        	$this->aMesages['target_name'] = $sName;
        	return $this;
        }
        public function message($sMessage)
        {
        	$this->aMesages['message'] = $sMessage;
        	return $this;
        }
        public function setMessageConfig($aConfigs)
        {
        	$this->aMesages['configs'] = $aConfigs;
        	return $this;
        }
        public function getResults()
        {
        	return $this->mResults;
        }
        public function getErrors()
        {
        	return $this->aErrors;
        }
        public function clean()
        {
        	$this->aMesages = array(
        		'from' => '',
        		'from_name' => '',
        		'target_id' => '',
        		'target_name' => '',
        		'message' => '',
        		'configs' => array(),
        	); 
        	$this->aErrors = array();
        	$this->sCallBackPoint = "";
        	return $this;
        }
        public function setError($sError)
        {
        	$this->aErrors[] = $sError;
        	return $this;
        }
        public function send()
        {
        	return false;
        }
        public function callback($sEndPoint)
        {
        	$this->sCallBackPoint = $sEndPoint;
        	return $this;
        }
        public function getForm()
        {
        	return false;
        }
        public function post($sEndPoint, $aData , $aHeaders = array())
        {
        	$ch = curl_init();
        	curl_setopt( $ch,CURLOPT_URL, $sEndPoint);
        	curl_setopt( $ch,CURLOPT_POST, true );
        	if(count($aHeaders))
        	{
        		curl_setopt( $ch,CURLOPT_HTTPHEADER, $aHeaders );
        	}
        	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $aData ) );
        	$result = curl_exec($ch );
        	
        	curl_close( $ch );
        	
        	return $result;
        }
        public function validate()
        {
        	if(!isset($this->aMesages['message']) || empty($this->aMesages['message']))
        	{
        		$this->aErrors[] = "Message Body cannot be empty";
        		return false;
        	}
        	
        	if(!isset($this->aMesages['target_id']) || empty($this->aMesages['target_id']))
        	{
        		$this->aErrors[] = "Must have at least 1 receiver";
        		return false;
        	}
        	return true;
        }
        public function postBack()
        {
        	if(!empty($this->sCallBackPoint))
        	{
        		$aReturnData = array(
        				'errors' => $this->aErrors,
        				'sent_data' => $this->aMesages
        		);
        		$this->post($this->sCallBackPoint, $aReturnData);
        	}
        	return $this;
        }
    }
    
?>