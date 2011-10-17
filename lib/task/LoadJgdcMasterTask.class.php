<?php

class LoadJgdcAddressMasterTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('filename', null, sfCommandOption::PARAMETER_OPTIONAL, 'file name', date('Ym')),
      // add your own options here
    ));

    $this->namespace        = 'jgdc';
    $this->name             = 'load-master';
    $this->briefDescription = '国土地理協会から購入できる住所マスターを読み込んでJgdcAddressMasterにインサートします';
    $this->detailedDescription = <<<EOF
The [jgdc:load-master|INFO] task load file to jgdc_address_master.
put file on data/csv/jgdc_address_master

You can set filename to select load files:

  [./symfony jgdc:load-master --env=test --filename=200109|INFO]

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $filepath = sfConfig::get('sf_data_dir') . '/csv/' . $options['filename'] . 'jgdc.csv';
    $this->logSection('jgdc', sprintf('Loading "%s" environment "%s"', $options['env'], $filepath));

    if (false === is_readable($filepath) || false === file_exists($filepath)) {
      throw new sfException(sprintf('jgdc_address_file "%s" does not exist (or can not readable).', $filepath));
    }

    $this->log('process start.');

    $reader = new sfCsvReader($filepath);
    $reader->open();
    $i = 1;
    while ($data = $reader->read()) {
      if (51 !== count($data)) {
        throw new sfException(sprintf('jgdc_master_file field count does not match 51. check file "%s" line %d.', $filepath, $i));
      }

      $jgdc_address = JgdcAddressMasterTable::getInstance()->findOneByNewMaCode($data[1]);
      if (false === $jgdc_address) {
        $jgdc_address = new JgdcAddressMaster;
      }
      $jgdc_address->setMaCode($data[0]);
      $jgdc_address->setNewMaCode($data[1]);
      $jgdc_address->setZipCode($data[2]);
      $jgdc_address->setBarcode($data[3]);
      $jgdc_address->setBarcodeLength($data[4]);
      $jgdc_address->setZipCodeAdditionalInfo1($data[5]);
      $jgdc_address->setZipCodeAdditionalInfo2($data[6]);
      $jgdc_address->setRelationFlag($data[7]);
      $jgdc_address->setRelationMaCode($data[8]);
      $jgdc_address->setNoPrefectureNameCode($data[9]);
      $jgdc_address->setPrefectureNameKana($data[10]);
      $jgdc_address->setCityNameKana($data[11]);
      $jgdc_address->setAreaNameKana($data[12]);
      $jgdc_address->setAddressKana($data[13]);
      $jgdc_address->setPrefectureNameKanaLength($data[14]);
      $jgdc_address->setCityNameKanaLength($data[15]);
      $jgdc_address->setAreaNameKanaLength($data[16]);
      $jgdc_address->setAddressKanaLength($data[17]);
      $jgdc_address->setTotalKanaLength($data[18]);
      $jgdc_address->setPrefectureName($data[19]);
      $jgdc_address->setCityName($data[20]);
      $jgdc_address->setAreaName($data[21]);
      $jgdc_address->setAddress($data[22]);
      $jgdc_address->setPrefectureNameLength($data[23]);
      $jgdc_address->setCityNameLength($data[24]);
      $jgdc_address->setAreaNameLength($data[25]);
      $jgdc_address->setAddressLength($data[26]);
      $jgdc_address->setTotalLength($data[27]);
      $jgdc_address->setJistype_prefectureName($data[28]);
      $jgdc_address->setJistypeCityName1($data[29]);
      $jgdc_address->setJistypeCityName2($data[30]);
      $jgdc_address->setJistypeAreaName1($data[31]);
      $jgdc_address->setJistypeAreaName2($data[32]);
      $jgdc_address->setJistypeAddress1($data[33]);
      $jgdc_address->setJistypeAddress2($data[34]);
      $jgdc_address->setAddressFlag1($data[35]);
      $jgdc_address->setAddressFlag2($data[36]);
      $jgdc_address->setNicknameType($data[37]);
      $jgdc_address->setNicknameFlag($data[38]);
      $jgdc_address->setOpenYearMonth($data[39]);
      $jgdc_address->setCloseYearMonth($data[40]);
      $jgdc_address->setNewMaCodeYearMonth($data[41]);
      $jgdc_address->setRenamedYearMonth($data[42]);
      $jgdc_address->setRenumberdZipCodeYearMonth($data[43]);
      $jgdc_address->setChangedBarcodeYearMonth($data[44]);
      $jgdc_address->setChangedRelationYearMonth($data[45]);
      $jgdc_address->setChangedNicknameFlagYearMonth($data[46]);
      $jgdc_address->setChangedAddressYearMonth($data[47]);
      $jgdc_address->setOldZipCode($data[48]);
      $jgdc_address->setUnnecessaryField($data[49]);
      $jgdc_address->setEditCode($data[50]);

      $jgdc_address->save();
      unset($jgdc_address);

      if (0 === $i % 100) $this->log(sprintf('%s records preceed.', $i));
      $i ++;
    }
    $reader->close();
  }
}
