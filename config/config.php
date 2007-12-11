<?php

sfPropelBehavior::registerHooks('sfPropelApprovableBehavior', array (
 ':save:post' => array ('sfPropelApprovableBehavior', 'postSave'),
 'Peer:doSelectRS' => array('sfPropelApprovableBehavior', 'doSelectRS'),
));

sfPropelBehavior::registerMethods('sfPropelApprovableBehavior', array (
  array (
    'sfPropelApprovableBehavior',
    'getApproval'
  ),
  array (
    'sfPropelApprovableBehavior',
    'getApprovalUrl'
  ),
));