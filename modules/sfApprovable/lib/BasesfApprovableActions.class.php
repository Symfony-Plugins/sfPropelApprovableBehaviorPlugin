<?php

class BasesfApprovableActions extends sfActions
{

  public function executeApprove()
  {
    $approval = sfApprovalPeer::retrieveByUuid($this->getRequestParameter('uuid'));
    if (!$approval)
    {
      return 'NotFound';
    }
  
    $ret = null;

    $object = $approval->getRelatedObject();
    $this->setFlash('sf_approvable_object', $object);

    $class = get_class($object);
    $peerClass = get_class($object->getPeer());

    $destination = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class.'_destination', '@homepage');
    $approvedValue = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class.'_approved_value', true);
    $columnName = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class.'_column', 'is_approved');
    
    $method = 'set'.call_user_func(array($peerClass, 'translateFieldName'), $columnName, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);

    foreach (sfMixer::getCallables('sfApprovableActions:approve:pre') as $callable)
    {
      $ret = call_user_func($callable, $object);
    }
    
    if (!is_null($ret))
    {
      return $ret;
    }

    $object->$method($approvedValue);
    $object->save();
    $approval->delete();

    foreach (sfMixer::getCallables('sfApprovableActions:approve:post') as $callable)
    {
      $ret = call_user_func($callable, $object);
    }

    if (!is_null($ret))
    {
      return $ret;
    }


    
    $this->redirect($destination);
  }

}
