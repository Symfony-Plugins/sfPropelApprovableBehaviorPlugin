<?php

/**
 * Subclass for performing query and update operations on the 'sf_approval' table.
 *
 * 
 *
 * @package plugins.sfPropelApprovableBehaviorPlugin.lib.model
 */ 
class PluginsfApprovalPeer extends BasesfApprovalPeer
{
  
  public static function retrieveOrCreateByObject(BaseObject $object)
  {
    if ($object->isNew() === true || $object->isModified() === true || $object->isDeleted() === true )
    {
      throw new Exception('You can only approve an object which has already been saved');
    }
    
    $columnName = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_column', 'is_approved');
    $approvedValue = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_approved_value', true);
    $disabledApplications = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_disabled_applications');
    
    // Test if we should crated an approval - is the column value different to out required value?
    // If our object is already approved then we can delete other
    $columnGetter = 'get'.sfInflector::camelize($columnName);
    if ($approvedValue!=$object->$columnGetter())
    {
      $c = new Criteria();
      $c->add(sfApprovalPeer::APPROVABLE_ID, $object->getPrimaryKey());
      $c->add(sfApprovalPeer::APPROVABLE_MODEL, get_class($object));
      $approval = sfApprovalPeer::doSelectOne($c);
  
      if (!$approval)
      {
        $approval = new sfApproval();
        $approval->setApprovableModel(get_class($object));
        $approval->setApprovableId($object->getPrimaryKey());
        $approval->setUuid(sfPropelApprovableToolkit::generateUuid());
        $approval->save();
      }

      return $approval;
    }
    else
    {
      sfApprovalPeer::deleteByObject($object);
      return null;
    }

    
    return $approval;
  }
  
  public static function deleteByObject(BaseObject $object)
  {
    $c = new Criteria();
    $c->add(sfApprovalPeer::APPROVABLE_ID, $object->getPrimaryKey());
    $c->add(sfApprovalPeer::APPROVABLE_MODEL, get_class($object));
    $approval = sfApprovalPeer::doDelete($c);
  }
  
  public function retrieveByUuid($uuid)
  {
    $c = new Criteria();
    $c->add(sfApprovalPeer::UUID, $uuid);
    return sfApprovalPeer::doSelectOne($c);
  }
  
}
