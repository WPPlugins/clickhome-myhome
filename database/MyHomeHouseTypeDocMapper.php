<?php

class MyHomeHouseTypeDocMapper extends MyHomeBaseMapper{
  protected static $TABLE='houseTypesDocs';

  protected static $ATTRIBUTES_FORMATS=[
    'houseid'=>self::TYPE_INT,
    'title'=>100,
    'type'=>10,
    'url'=>self::TYPE_INT,
    'order'=>100
  ];
}
