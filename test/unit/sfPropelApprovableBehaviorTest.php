<?php

/**
 * Unit tests for the sfPropelApprovableBehavior plugin.
 *
 * Despite running unit tests, we use the functional tests bootstrap to take advantage of propel
 * classes autoloading...
 * 
 * In order to run the tests in your context, you have to copy this file in a symfony test directory
 * and configure it appropriately (see the "configuration" section at the beginning of the file)
 *  
 * @author   Michael Nolan <michael.nolan@edgehill.ac.uk>
 */

// configuration
// -- an existing application name
$app = 'frontend';

// -- the model class the tests should use
$approvable_class = 'sfComment';
$approvable_column = 'is_approved';
$approvable_setter = 'setIsApproved';
$approvable_getter = 'getIsApproved';
$approved_value = true;

// -- path to the symfony project where the plugin resides
$sf_path = dirname(__FILE__).'/../../../..';
 
// bootstrap
include($sf_path . '/test/bootstrap/functional.php');

// create a new test browser
$browser = new sfTestBrowser();
$browser->initialize();

// initialize database manager
$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$con = Propel::getConnection();

$approvable_peer_class = $approvable_class.'Peer';

// cleanup database
call_user_func(array($approvable_peer_class, 'doDeleteAll'));

// register behavior on test object




// Now we can start to test
$t = new lime_test(18, new lime_output_color());

$t->diag('new methods');
$methods = array(
  'getApprovalUrl',
  'getApproval',
);
foreach ($methods as $method)
{
  $t->ok(is_callable($approvable_class, $method), sprintf('Behavior adds a new %s() method to the object class', $method));
}
$item1 = new $approvable_class();
try {
  $item1->getApprovalUrl();
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('getApprovalUrl() on an unsaved class throws an exception');
}
try {
  $item1->getApproval();
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('getApproval() on an unsaved class throws an exception');
}
try {
  sfApprovalPeer::retrieveOrCreateByObject($item1);
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('sfApprovalPeer::retrieveOrCreateByObject() on an unsaved class throws an exception');
}
$item1->save();
$t->isa_ok($item1->getApproval(), 'sfApproval', 'getApproval() returns and sfApproval object');
$t->isa_ok($item1->getApprovalUrl(), 'string', 'getApprovalUrl() returns a string');
$t->like($item1->getApprovalUrl(), '|^sfApproval/approve\?uuid=[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$|', 'getApprovalUrl() returns correctly formatted internal URL');
$item1->$approvable_setter($approved_value);
try {
  $item1->getApprovalUrl();
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('getApprovalUrl() on an unsaved class throws an exception');
}
try {
  $item1->getApproval();
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('getApproval() on an unsaved class throws an exception');
}
try {
  sfApprovalPeer::retrieveOrCreateByObject($item1);
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('sfApprovalPeer::retrieveOrCreateByObject() on an unsaved class throws an exception');
}
$item1->save();
$t->is($item1->getApproval(), null, 'getApproval() returns null for approved objects');
$t->is($item1->getApprovalUrl(), false, 'getApprovalUrl() returns false for approved objects');
$item1->$approvable_setter(!$approved_value);
$item1->save();
$t->isa_ok($approval = $item1->getApproval(), 'sfApproval', 'Unapproved getApproval() returns and sfApproval object');
$t->isa_ok($item1->getApprovalUrl(), 'string', 'Unapproved getApprovalUrl() returns a string');
$t->isa_ok(sfApprovalPeer::retrieveOrCreateByObject($item1), 'sfApproval', 'retrieveOrCreateByObject() returns an sfApproval object');
$t->isa_ok(sfApprovalPeer::retrieveByUuid($approval->getUuid()), 'sfApproval', 'sfApprovalPeer::retrieveByUuid() returns an instance of sfApproval');
$item1->delete();
try {
  $item1->getApprovalUrl();
  $t->fail('no code should be executed after throwing exception');
} catch (Exception $err) {
  $t->pass('getApprovalUrl() on an deleted class throws an exception');
}




