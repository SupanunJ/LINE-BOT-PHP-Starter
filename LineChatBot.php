<?php
$access_token = 's/m2qnXnrLyOpbmE+aJ71nNBy1k2ZBJQaoBZN6e26iDAVdZ+BS510Z4fX6Wa8e9q72LLyTfQ3mrRhW3Y4Llr/SJ8J57kt5STaOI7uXzgqFYTpgLqPFVRLKRjsSmPfw93P/OhsfIjqlyUJTL007RLXgdB04t89/1O/w1cDnyilFU=';
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events']))
{
	// Loop through each event
	foreach ($events['events'] as $event)
	{
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text')
		{
			$getText = $event['message']['text'];
			$userID = $event['source']['userId'];
			if ($getText=='สมัครสมาชิก')
			{
				$replyToken = $event['replyToken'];
        replyMessage($replyToken,textBuild('กรุณากรอกข้อมูลที่เป็นจริงเพื่อท่านจะได้รับบริการที่ถูกต้อง'),$access_token);
        pushMessage($userID,textBuild('กรุณากรอกข้อมูลดังต่อไปนี้ : สมัครสมาชิก,ชื่อ,นามสกุล,เบอร์โทร์ศัพท์ที่ติดต่อได้,บ้านเลขที่,ซอย,หมู่บ้าน,แขวง,อำเภอ,จังหวัด,รหัสไปรษณีย์,ข้อมูลอื่นๆ'),access_token);
        pushMessage($userID,textBuild('กรณีที่ ที่อยู่ของท่าน มีหมายเลขห้องหรือชั้นด้วย กรุณาใส่ใน ข้อมูลอื่นๆ'),$access_token);
			}
			else if (strpos($getText,"สมัครสมาชิก,")!==false)
			{
				$text = str_replace('สมัครสมาชิก','',$getText);
				$register = explode(',',$text);
				$iCount = count($register);
				$inform = ['ชื่อ','นามสกุล','เบอร์โทรศัพท์ที่สามารถติดต่อได้','บ้านเลขที่','ซอย','หมู่บ้าน','แขวง','อำเภอ','จังหวัด','รหัสไปรษณีย์','ข้อมูลอื่นๆ'];
				$ansText = '';
				for ($i = 0; $i<$iCount-1; $i++)
				{
					$ansText = $ansText.'   '.$inform[$i] . ' : ' . $register[$i+1];
				}
				$text1 = $ansText;
        pushMessage($userID,textBuild($text1),$access_token);
			}
			else if ($getText=='ดูเมนูและสั่งซื้อสินค้า'||$getText=='ดูเมนู'||$getText=='สั่งซื้อ')
			{
				$replyToken = $event['replyToken'];
        replyMessage($replyToken,textBuild('บริการนี้ยังไม่เปิดใช้บริการ').$access_token);
			}
			else if ($getText=='ดูข้อมูลร้านค้า')
			{
        pushMessage($userID,textBuild('ร้านขนมข้าวตังเสวยแม่ณี  สามารถติดต่อทางร้านได้ที่เบอร์  0818178962 ทางร้านขอขอบพระคุณลูกค้าทุกท่านที่ใช้บริการ'),$access_token);
			}
			else if ($getText=='ดูข้อมูลส่วนตัว')
			{
        pushMessage($userID,textBuild('หากท่านต้องการแก้ไขข้อมูลส่วนตัวของท่าน กรุณาพิมพ์ตามรูปแบบการแก้ไขดังนี้ิ'),$access_token);
        pushMessage($userID,textBuild('แก้ไข/สิ่งที่ท่านต้องการแก้ไข/ข้อมูลที่แก้ไชแล้ว เช่น ท่านต้องการแก้ไขเบอร์โทรศัพท์ จะต้องพิมพ์ดังนี้ แก้ไข/เบอร์โทรศัพท์/0812345678 เป็นต้น'),$access_token);
        confirmBuild($userID,$access_token);
			}
		}
	}
}
function textBuild($text)
{
  $messages = [
			'type' => 'text',
			'text' => $text
			];
  return $messages;
}
function confirmBuild ($userID,$access_token)
{
	$messages = [
      "type" => "template",
      "altText" => "this is a confirm template",
      "template" => array("type" => "confirm","text" => "Are you sure?",
                          "actions" => array("type" => "message","label" => "Yes","text" => "yes"),
                                       array("type" => "message","label" => "Yes","text" => "yes"))
  ];
  pushMessage($userID,$messages,$access_token);
}
function replyMessage($replyToken,$messages,$access_token)
{
  $data = [
		'replyToken' => $replyToken,
		'messages' => [$messages]
			];
	exec_url($data,$access_token,'https://api.line.me/v2/bot/message/reply');
}
function pushMessage($userID,$messages,$access_token)
{
  $data = [
		'to' => $userID,
		'messages' => [$messages],
		];
	exec_url($data,$access_token,'https://api.line.me/v2/bot/message/push');
}
function exec_url($data,$access_token,$url)
{
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result . "\r\n";
}
