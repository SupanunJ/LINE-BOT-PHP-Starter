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
				$text = 'กรุณากรอกข้อมูลที่เป็นจริงเพื่อท่านจะได้รับบริการที่ถูกต้อง';
				replypattern($replyToken,$text,$access_token);

				$text1 = 'กรุณากรอกข้อมูลดังต่อไปนี้ : สมัครสมาชิก,ชื่อ,นามสกุล,เบอร์โทร์ศัพท์ที่ติดต่อได้,บ้านเลขที่,ซอย,หมู่บ้าน,แขวง,อำเภอ,จังหวัด,รหัสไปรษณีย์,ข้อมูลอื่นๆ';
				pushpattern($userID,$text1,$access_token);

				$text2 = 'กรณีที่ ที่อยู่ของท่าน มีหมายเลขห้องหรือชั้นด้วย กรุณาใส่ใน ข้อมูลอื่นๆ';
				pushpattern($userID,$text2,$access_token);
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
				pushpattern($userID,$text1,$access_token);
			}

			else if ($getText=='ดูเมนูและสั่งซื้อสินค้า'||$getText=='ดูเมนู'||$getText=='สั่งซื้อ')
			{
				// Get replyToken
				$replyToken = $event['replyToken'];
				$text = 'บริการนี้ยังไม่เปิดใช้บริการ';
				replypattern($replyToken,$text,$access_token);
			}

			else if ($getText=='ดูข้อมูลร้านค้า')
			{
				$text1 = 'ร้านขนมข้าวตังเสวยแม่ณี  สามารถติดต่อทางร้านได้ที่เบอร์  0818178962 ทางร้านขอขอบพระคุณลูกค้าทุกท่านที่ใช้บริการ';
				pushpattern($userID,$text1,$access_token);
			}

			else if ($getText=='ดูข้อมูลส่วนตัว')
			{

				pushButton ($userID,$access_token);
				replyButton ($replyToken,$access_token);
				/*$text1 = 'หากท่านต้องการแก้ไขข้อมูลส่วนตัวของท่าน กรุณาพิมพ์ตามรูปแบบการแก้ไขดังนี้';
				pushpattern($userID,$text1,$access_token);

				$text2 = 'แก้ไข/สิ่งที่ท่านต้องการแก้ไข/ข้อมูลที่แก้ไชแล้ว เช่น ท่านต้องการแก้ไขเบอร์โทรศัพท์ จะต้องพิมพ์ดังนี้ แก้ไข/เบอร์โทรศัพท์/0812345678 เป็นต้น';
				pushpattern($userID,$text2,$access_token);*/


				/*$replyToken = $event['replyToken'];
				replyButton($replyToken,$access_token);*/
			}
		}
	}
}
function replypattern($replyToken,$text,$access_token)
{
	$messages = [
			'type' => 'text',
			'text' => $text
			];
	// Make a POST Request to Messaging API to reply to sender
	$data = [
		'replyToken' => $replyToken,
		'messages' => [$messages],
			];
	replyMessage($data,$access_token);
}
function pushpattern ($userID,$text,$access_token)
{
	$messages = [
		'type' => 'text',
		'text' => $text
		];
	$data = [
		'to' => $userID,
		'messages' => [$messages],
		];
	pushMessage($data,$access_token);
}

function pushButton ($userID,$access_token)
{
	$actions = [
		'type' => 'message','label' => 'Yes','text' => 'yes'
		];
	$template = [
		'type' => 'confirm',
		'text' => 'Are you sure?',
		'actions' => [$actions]
		];
	$messages = [
		'type' => 'template',
		'altText' =>'this is a confirm template',
		'template' => [$template]
		];
	$data = [
		'to' => $userID,
		'messages' => [$messages]
		];
	pushMessage ($data,$access_token);
}

function replyButton ($replyToken,$access_token)
{
	$actions = [
		['type' => 'message','label' => 'Yes','text' => 'yes'],
		['type' => 'message','label' => 'No','text' => 'no']
		];
	$template = [
		'type' => 'confirm',
		'text' => 'Are you sure?',
		'actions' => [$actions]
		];
	$messages = [
		'type' => 'template',
		'altText' =>'this is a confirm template',
		'template' => [$template]
		];
	$data = [
		'replyToken' => $replyToken,
		'messages' => [$messages]
			];
	replyMessage ($data,$access_token);
}

function replyMessage($data,$access_token)
{
	exec_url($data,$access_token,'https://api.line.me/v2/bot/message/reply');
}
function pushMessage($data,$access_token)
{
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
