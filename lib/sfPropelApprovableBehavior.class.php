<?php

class sfPropelApprovableBehavior
{

  static protected $active = true;


  public function postSave(BaseObject $object)
  {
    $columnName = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_column', 'is_approved');
    $approvedValue = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_approved_value', true);
    $disabledApplications = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.get_class($object).'_disabled_applications');
    
    // Test if we should crated an approval - is the column value different to out required value?
    // If our object is already approved then we can delete other
    $columnGetter = 'get'.sfInflector::camelize($columnName);
    if ($approvedValue!=$object->$columnGetter())
    {
      $approval = sfApprovalPeer::retrieveOrCreateByObject($object);
    }
    else
    {
      sfApprovalPeer::deleteByObject($object);
    }
  }

  public function doSelectRS($class, $criteria, $con = null)
  {
    preg_match('/Base(.*)Peer/', $class, $class_name);
    $columnName = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class_name[1].'_column', 'is_approved');
    $approvedValue = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class_name[1].'_approved_value', true);
    $disabledApplications = sfConfig::get('propel_behavior_sfPropelApprovableBehavior_'.$class_name[1].'_disabled_applications');
    if (self::$active && !in_array(SF_APP, $disabledApplications))
    {
      $criteria->add(call_user_func(array($class, 'translateFieldName'), $columnName, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME), $approvedValue);
    }
    else
    {
      self::$active = true;
    }

    return false;
  }

  public function getApprovalUrl(BaseObject $object)
  {
    if ($object->isNew() === true || $object->isModified() === true || $object->isDeleted() === true )
    {
      throw new Exception('You can only approve an object which has already been saved');
    }
    
    $approval = $this->getApproval($object);
    if ($approval)
    {
      return 'sfApprovable/approve?uuid='.$approval->getUuid();
    }
    else
    {
      return false;
    }

  }

  public function getApproval(BaseObject $object)
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
      return sfApprovalPeer::retrieveOrCreateByObject($object);
    }
    else
    {
      sfApprovalPeer::deleteByObject($object);
      return null;
    }
  }

  public static function disable()
  {
    self::$active = false;
  }


}