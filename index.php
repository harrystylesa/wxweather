<?php
namespace app\wx\controller;
include('../../application/wx/controller/Index.php');
include('../../application/wx/controller/Getweather.php');
    //获得参数 signature nonce token timestamp echostr
    $nonce     = $_GET['nonce'];
    $token     = 'excuseme';
    $timestamp = $_GET['timestamp'];
    $echostr   = $_GET['echostr'];
    $signature = $_GET['signature'];
    //形成数组，然后按字典序排序
    $array = array();
    $array = array($nonce, $timestamp, $token);
    sort($array);
    //拼接成字符串,sha1加密 ，然后与signature进行校验
    $str = sha1( implode( $array ) );
    if( $str == $signature && $echostr ){
        //第一次接入weixin api接口的时候
        echo  $echostr;
        exit;
    } 
	$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
	$postObj = simplexml_load_string( $postArr );
	if( strtolower( $postObj->MsgType) == 'event'){
      if( strtolower($postObj->Event == 'subscribe') ){
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  =  'text';
        $content  = '欢迎关注我们的微信公众账号';
        $template = "<xml>
        				<ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                     </xml>";
        $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        echo $info;
      }
    }
	if(strtolower( $postObj->MsgType) == 'text'){
      $content = $postObj->Content;
      $toUser   = $postObj->FromUserName;
      $fromUser = $postObj->ToUserName;
      $time     = time();
      $msgType  =  'text';
  //    if(strpos($content,'天气') !== false){
  
        $getweather = new Getweather();
        $weahter = $getweather->getweather($content,$weather);
           if($weather!=null){
      //  echo $obj->data->yesterday->date;
        $date = $weather['data']['forecast'][0]['date'];
        $tianqi = $weather['data']['forecast'][0]['type'];
        $high = $weather['data']['forecast'][0]['high'];
        $low = $weather['data']['forecast'][0]['low'];
        $fengxiang =$weather['data']['forecast'][0]['fengxiang'];
        $fengli=substr($weather['data']['forecast'][0]['fengli'],9,12);
        $aqi =$weather['data']['forecast'][0]['aqi'];
        $advice=$weather['data']['ganmao'];
        $content = '今天是'.$date.'，天气'.$tianqi.'，'.$low.', '.$high.'，'.'风力'.$fengli.'，空气质量指数'.$aqi.','.$advice;
         $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        echo $info;
     }
     else{
       //$fp=fopen("./data.txt","a");
      // $str=$postObj->Content
       	$content = $postObj->Content;
                $toUser   = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time     = time();
                $msgType  =  'text';
                $content  = '您发送的内容是：'.$content;
                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
     	}
        
      }
   