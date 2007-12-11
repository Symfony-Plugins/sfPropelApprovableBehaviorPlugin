<?php

/**
 * Subclass for representing a row from the 'sf_approval' table.
 *
 * 
 *
 * @package plugins.sfPropelApprovableBehaviorPlugin.lib.model
 */ 
class PluginsfApproval extends BasesfApproval
{
  
  public function getRelatedObject()
  {
    sfPropelApprovableBehavior::disable();
    $peer = $this->getApprovableModel().'Peer';
    return call_user_func(array($peer, 'retrieveByPK'), $this->getApprovableId());
  }
  
}
