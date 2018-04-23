<?php

namespace App\Tool\SMS;
//引入自定义返回格式
use App\Models\M3Result;

class SendTemplateSMS
{

    //控制台主页开发者主账号查看 请求地址和端口用改 添加相应的测试号码即可
  //主帐号
  private $accountSid='8a216da862dcd1050162e10e2481022a';

  //主帐号Token
  private $accountToken='3211495366bd46159127ca77e4b32500';

  //应用Id
  private $appId='8a216da862dcd1050162e10e24d80230';

  //请求地址，格式如下，不需要写https://
  private $serverIP='sandboxapp.cloopen.com';

  //请求端口
  private $serverPort='8883';

  //REST版本号
  private $softVersion='2013-12-26';

  /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
  public function sendTemplateSMS($to,$datas,$tempId)
  {
      //自定义接口接收返回值
       $m3_result = new M3Result;

       // 初始化REST SDK
       $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);
       $rest->setAccount($this->accountSid,$this->accountToken);
       $rest->setAppId($this->appId);

       // 发送模板短信
      //  echo "Sending TemplateSMS to $to <br/>";
       $result = $rest->sendTemplateSMS($to,$datas,$tempId);
       if($result == NULL ) {
           //返回值为空设置结果接口状态和学习
           $m3_result->status = 3;
           $m3_result->message = 'result error!';
       }
       if($result->statusCode != 0) {
           //接收它的返回值和状态
           $m3_result->status = $result->statusCode;
           $m3_result->message = $result->statusMsg;
       }else{
           //通过的状态
           $m3_result->status = 0;
           $m3_result->message = '发送成功';
       }

       //返回结果对象 方便控制器使用
       return $m3_result;
  }
}
//三个参数第一个号码 第二个验证码以及有效分钟数 第三个验证平台中的模板
//sendTemplateSMS("18576437523", array(1234, 5), 1);
