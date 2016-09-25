<?php

require_once('../config.php');

require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');

//Specify QBO or QBD
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
	exit("Please add realm to App.Config before running this sample.<br/>");

// Prep Service Context
$requestValidator = new OAuthRequestValidator(ConfigurationManager::AppSettings('AccessToken'),
                                              ConfigurationManager::AppSettings('AccessTokenSecret'),
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));

$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
	exit("Problem while initializing ServiceContext.<br/>");

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
	exit("Problem while initializing DataService.<br/>");

// Iterate through all Customers, even if it takes multiple pages
$i = 1;
while (1) {
	$allCustomers = $dataService->FindAll('Customer', $i, 500);

	if (!$allCustomers || (0==count($allCustomers)))
		break;

	foreach($allCustomers as $oneCustomer)
	{
		echo "<h3>Customer ".($i++)." name: {$oneCustomer->DisplayName}</h3>";
		echo "\t * Id: [{$oneCustomer->Id}]<br/>";
		echo "\t * Active: [{$oneCustomer->Active}]<br/>";
		echo "\t * Open Balance: [\${$oneCustomer->BalanceWithJobs}]<br/>";
		echo "\t * Phone: [{$oneCustomer->PrimaryPhone->FreeFormNumber}]<br/>";
		var_dump($oneCustomer);
		echo "<br/>";
	}
}

/*

Example output:

Customer[0]: JIMCO
	 * Id: [NG:953957]
	 * Active: [true]

Customer[1]: ACME Corp
	 * Id: [NG:953955]
	 * Active: [true]

Customer[2]: Smith Inc.
	 * Id: [NG:952359]
	 * Active: [true]


...

*/
?>
