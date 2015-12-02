<?php
    namespace SimplePush;
     
    class Mail extends \SimplePush\Provider{
        private $oMailer;
        private $aCCMails;
        private $aBBCMails;
        private $aAttachFiles;
        public function __construct($aConfigs = array())
        {
            $this->sName = "Mail";
            $this->sVersion = "1.0";
            $sFileName =  $this->getBasePath().DIRECTORY_SEPARATOR. 'lib'.
            			 DIRECTORY_SEPARATOR. 'swiftmailer' .DIRECTORY_SEPARATOR.'lib'. DIRECTORY_SEPARATOR . 'swift_required.php';
            
            if(!file_exists($sFileName))
            {
            	return false;
            }
            require_once $sFileName;
            parent::__construct($aConfigs);
            $sMethod = isset($this->aConfigs['method'])?$this->aConfigs['method']:"mail";
            
            if($sMethod == "smtp")
            {
            	$sPort = isset($this->aConfigs['port'])?$this->aConfigs['port']:25;
            	$mSecure = isset($this->aConfigs['authenticate'])?$this->aConfigs['authenticate']:"tls";
            	
            	$sHost = isset($this->aConfigs['host'])?$this->aConfigs['host']:"localhost";
            	$sUserName = isset($this->aConfigs['user'])?$this->aConfigs['user']:"";
            	$sPassword = isset($this->aConfigs['password'])?$this->aConfigs['password']:"";
            	$transport = \Swift_SmtpTransport::newInstance($sHost,$sPort, $mSecure);
            	$transport->setUserName($sUserName);
            	$transport->setPassword($sPassword);
            	
            }
            else
            {
            	$transport = \Swift_MailTransport::newInstance();
            }
            $this->oMailer = \Swift_Mailer::newInstance($transport);
           
        }
        public function subject($sSubject)
        {
        	$this->aMesages['subject'] = $sSubject;
        	return $this;
        }
        public function clean()
        {
        	parent::clean();
        	$this->aMesages['subject'] ='';
        	$this->aBBCMails = array();
        	$this->aCCMails = array();
        	$this->aAttachFiles = array();
        	return $this;
        }
        /**
         * 
         * @param unknown $aData
         * Example:array("a.derosa@audero.it" => "Aurelio De Rosa")
         */
        public function cc($aData = array(), $bForce = false)
        {
        	if($bForce == true)
        	{
        		$this->aCCMails = $aData;
        	}
        	else
        	{
        		$this->aCCMails = array_merge($this->aCCMails, $aData);
        	}
        	return $this;
        }
        /**
         *
         * @param unknown $aData
         * Example:array("a.derosa@audero.it" => "Aurelio De Rosa")
         */
        public function bcc($aData = array(), $bForce = false)
        {
        	if($bForce == true)
        	{
        		$this->aBBCMails = $aData;
        	}
        	else
        	{
        		$this->aBBCMails = array_merge($this->aBBCMails, $aData);
        	}
        	return $this;
        }
        public function attach($sFileName)
        {
        	$this->aAttachFiles[] = $sFileName;
        	return $this;
        }
        /**
         *
         * @see \SimplePush\Provider::send()
         */
        public function send()
        {
        	
        	if(!$this->validate())
        	{
        		return false;
        	}
        	try{
        		
        		$message = \Swift_Message::newInstance();
        		
        		$message->setTo(array(
        				$this->aMesages['target_id'] => $this->aMesages['target_name'],
        		));
        		
        		if( is_array($this->aCCMails) && count($this->aCCMails))
        		{
        			$message->setCc($this->aCCMails);
        		}
        		if( is_array($this->aBBCMails) && count($this->aBBCMails))
        		{
        			$message->setBcc($this->aBBCMails);
        		}
        		
        		$message->setSubject($this->aMesages['message']['subject']);
        		$message->setBody($this->aMesages['message']['content']);
        		if(isset($this->aMesages['message']['from']) && !empty($this->aMesages['message']['from']) 
        				&& isset($this->aMesages['message']['from_name']) && isset($this->aMesages['message']['from_name']))
        		{
        			
        			$message->setFrom($this->aMesages['message']['from'], $this->aMesages['message']['from_name']);
        		}
        		if( is_array($this->aAttachFiles) && count($this->aAttachFiles))
        		{
        			foreach($this->aAttachFiles as $iKey => $sFile)
        			{
        				if(file_exists($sFile))
        				{
        					$message->attach(\Swift_Attachment::fromPath($sFile));
        				}
        			}
        		}
        		$this->oMailer->send($message, $failedRecipients);
        		$this->mResults = $failedRecipients;
        		$this->aErrors = $failedRecipients;
        		
        	}catch(Exception $ex)
        	{
        		$this->setError('['.$ex->getCode().']'. $ex->getMessage());
        	}
        	
        	$this->postBack();
        	$this->clean();
        }
       
    }
?>