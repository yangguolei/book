<?php

namespace App\Models;

class M3Email {
//邮件模板类  对应laravel的邮件发送方法 需要的属性
  public $from;  // 发件人邮箱
  public $to; // 收件人邮箱
  public $cc; // 抄送
  public $attach; // 附件
  public $subject; // 主题
  public $content; // 内容

}
