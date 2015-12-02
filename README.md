# simplepush

1. Send mail:
use SimplePush;
$aConfigs = array(
	'method' => smtp //or mail
    'host' => smtp.gmail.com
    'user' => hello@fwebshop.com
    'password' => hellopass
    'authenticate' => tls // or ssl
    'port' => 587
);
$aMessage = array(
	'subject' => "Hello Mail"
    'content' => "Mail content"
    'from' => hello@fwebshop.com

);
$oInstance = new SimplePush\Mail($aConfigs);
$oInstance->message($aMessage);
$oInstance->to("nicetomeetyou@fwebshop.com");
$oInstance->send();
$mResults = $oInstance->getResults();
$aTmpError = $oInstance->getErrors();
