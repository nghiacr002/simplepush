# simplepush

1. Send mail:

```
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
```
2. Push to Android device
```
use SimplePush;
$aConfigs = array(
    'app_mode' => "production" //or development
    'api_access_key' => "Project API Key got from Google"
    
);
$aMessage = array(
   'command' => '2',
   'message' => 'Hello Android', 
   'your_param' => 'your_value'

);
$oInstance = new SimplePush\Android($aConfigs);
$oInstance->message($aMessage);
$oInstance->to("device_token_id");
$oInstance->send();
$mResults = $oInstance->getResults();
$aTmpError = $oInstance->getErrors();
```
3. Push to iOS device

```
use SimplePush;
$aConfigs = array(
    'app_mode' => "production" //or development
    'certification_file' => "Link to pem file"
    'password' => "" // passphrase for certificated file. 
   
);
$aMessage = array(
   'aps' => array(
      'alert' => 'Hello iOS',
      'sound' => 'default'
   )

);
$oInstance = new SimplePush\IOS($aConfigs);
$oInstance->message($aMessage);
$oInstance->to("device_token_id");
$oInstance->send();
$mResults = $oInstance->getResults();
$aTmpError = $oInstance->getErrors();
```
