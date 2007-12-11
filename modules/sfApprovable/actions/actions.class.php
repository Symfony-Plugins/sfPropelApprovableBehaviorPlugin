<?php


// autoloading for plugin lib actions is broken as at symfony-1.0.2
require_once(sfConfig::get('sf_plugins_dir'). '/sfPropelApprovableBehaviorPlugin/modules/sfApprovable/lib/BasesfApprovableActions.class.php');

class sfApprovableActions extends BasesfApprovableActions
{
}
