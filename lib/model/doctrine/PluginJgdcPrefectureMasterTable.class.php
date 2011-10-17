<?php

/**
 * PluginJgdcPrefectureMasterTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginJgdcPrefectureMasterTable extends Doctrine_Table
{
  /**
   * Return Prefecture name
   * 
   * @param string Prefecture code (from '01' to '47')
   * @return mixed Prefecture name, but if prefecture code is invalid, return false.
   */
  public static function getNameByCode($code)
  {
    $model = Doctrine_Core::getTable('JgdcPrefectureMaster')->findOneByCode($code);
    if (false === $model) {
      return false;
    }

    return $model->getName();
  }

  /**
   * Return All Prefecture list
   *
   * @return array (prefecture_code => name, ....)
   */
   public static function getPrefectures()
   {
     return
       Doctrine_Core::getTable('JgdcPrefectureMaster')
       ->createQuery()
       ->select('code, name')
       ->orderBy('display_order')
       ->execute()
       ->toKeyValueArray('code', 'name');
   }
}
