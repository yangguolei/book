<?php

namespace App\Models;//指定命名空间

class M3Result {

    //接口 一条状态对应一条信息
  public $status;//状态 0通过
  public $message;//信息返回

    //转换json字符串
  public function toJson()
  {
      //因为这是我们自制的 laravel框架不会识别 所以要把他转换成json字符串
    return json_encode($this, JSON_UNESCAPED_UNICODE);
  }

}
