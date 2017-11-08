<?php

//Connect DB
$host = 'ec2-54-221-254-72.compute-1.amazonaws.com';
$dbname = 'de6sfosesim5hp';
$user = 'sicngsjfdewwql';
$pass = 'b5cf4b4612c0625c4e9ce261c84939a5bb33bf66dd5a95cd12b5fc1792019b6d';
$connention = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);

//Connect Line MessagingDPI
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

			if($getText=='ยังไง')
			{
				$result = $connention->query("SELECT line_id FROM customer WHERE line_id = 'Supanun'");
				$textquery = sprintf("%s",$result);
				if($result!=null)
				{
					pushMessage($userID,textBuild('มันไม่เป็นNULLเว้ย'),$access_token);
					pushMessage($userID,textBuild($result->rowCount()),$access_token);
					pushMessage($userID,textBuild($textquery),$access_token);
				}
			}

			if ($getText=='สมัครสมาชิก')
			{
				$replyToken = $event['replyToken'];
        replyMessage($replyToken,textBuild('กรุณากรอกข้อมูลที่เป็นจริงเพื่อคุณจะได้รับบริการที่ถูกต้อง'),$access_token);
        pushMessage($userID,textBuild('กรุณากรอกข้อมูลดังต่อไปนี้ : สมัครสมาชิก,ชื่อ,นามสกุล,เบอร์โทร์ศัพท์ที่ติดต่อได้,บ้านเลขที่,ซอย,หมู่บ้าน,แขวง,อำเภอ,จังหวัด,รหัสไปรษณีย์,ข้อมูลอื่นๆ'),$access_token);
        pushMessage($userID,textBuild('*กรณีที่ ที่อยู่ของคุณ มีหมายเลขห้องหรือชั้นด้วย กรุณาใส่ใน ข้อมูลอื่นๆ'),$access_token);
        pushMessage($userID,textBuild('**กรณีที่ ข้อมูลใดไม่มีให้คุณใส่ ไม่มี หรือ - '),$access_token);
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

        pushMessage($userID,textBuild('คุณได้ทำการสมัครสมาชิกเรียบร้อยแล้ว คุณสามารถใช้งานบริการต่างๆได้ทันที'),$access_token);
			}
			else if ($getText=='ดูเมนูและสั่งซื้อสินค้า'||$getText=='ดูเมนู'||$getText=='สั่งซื้อ')
			{
				$replyToken = $event['replyToken'];
        replyMessage($replyToken,textBuild('บริการนี้ยังไม่เปิดใช้บริการ'),$access_token);
			}
			else if ($getText=='ดูข้อมูลร้านค้า')
			{
				$imageOrigi ='https://scontent-kut2-1.xx.fbcdn.net/v/t1.0-9/22814405_1701979389873235_1671810247852454191_n.jpg?oh=53a9387cb9c73b3ccf62a3534d62e6b0&oe=5A782E36';
				$imagePreview ='https://scontent-kut2-1.xx.fbcdn.net/v/t1.0-9/22814405_1701979389873235_1671810247852454191_n.jpg?oh=53a9387cb9c73b3ccf62a3534d62e6b0&oe=5A782E36';
				pushMessage($userID,imageBuild($imageOrigi,$imagePreview),$access_token);
				pushMessage($userID,textBuild('ขนมข้าวตังเสวยเป็นขนมโบราณหากินได้อยาก มีรสชาติแสนอร่อย ด้วยสูตรลับของท่านร้านทำมให้ขนมมีความกรอบ หอมหวาน กลมกล่อม นึกถึงขนมข้าวตังเสวยต้องนึกถึงร้านเมณีเท่านั้น!'),$access_token);
				pushMessage($userID,textBuild('ท่านสามารถติดต่อทางร้านได้โดยช่องทางดังนี้'),$access_token);
				pushMessage($userID,locationBuild(),$access_token);
        pushMessage($userID,textBuild('เบอรโทรศัพท์ 0817349462 และ 0818178962'),$access_token);

			}
			else if ($getText=='ดูข้อมูลส่วนตัว')
			{
				pushMessage($userID,textBuild('ข้อมูลของคุณคือ'),$access_token);
				pushMessage($userID,confirmBuild('คุณต้องการแก้ไขข้อมูลส่วนตัวของคุณหรือไม่','ต้องการ','ฉันต้องการแก้ไขข้อมูล','ไม่ต้องการ','ฉันไม่ต้องการแก้ไขข้อมูล'),$access_token);
			}
			else if ($getText=='ฉันต้องการแก้ไขข้อมูล')
			{
				pushMessage($userID,textBuild('หากคุณต้องการแก้ไขข้อมูลส่วนตัวของคุณ กรุณาพิมพ์ตามรูปแบบการแก้ไขดังนี้'),$access_token);
        pushMessage($userID,textBuild('แก้ไข,สิ่งที่คุณต้องการแก้ไข,ข้อมูลที่แก้ไขแล้ว เช่น คุณต้องการแก้ไขเบอร์โทรศัพท์ จะต้องพิมพ์ดังนี้ แก้ไข,เบอร์์,0812345678 เป็นต้น'),$access_token);
				pushMessage($userID,textBuild('ข้อมูลที่ท่านสามารถแก้ไขได้มีดังนี้ ชื่อ,นามสกุล,เบอร์,บ้านเลขที่,ซอย,หมู่บ้าน,แขวง,อำเภอ,จังหวัด,รหัสไปรษณีย์,ข้อมูลอื่นๆ'),$access_token);
			}
			else if (strpos($getText,'แก้ไข,ชื่อ')!==false)
			{
				pushMessage($userID,textBuild('ระบบได้แก้ไขข้อมูลของคุณเรียบร้อยแล้ว'),$access_token);
			}
		}
    if ($event['type'] == 'message' && $event['message']['type'] == 'sticker')
    {
      $userID = $event['source']['userId'];
      pushMessage($userID,stickerBuild(),$access_token);
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

function stickerBuild()
{
  $messages = [
      'type' => 'sticker',
      'packageId' => '1',
      'stickerId' => '1'
  ];
  return $messages;
}

function imageBuild($original,$preview)
{
  $messages = [
      'type' => 'image',
      'originalContentUrl' => $original,
      'previewImageUrl' => $preview
  ];
  return $messages;
}

function locationBuild()
{
  $messages = [
      'type' => 'location',
      'title' => 'Shop Location',
      'address' => 'ร้านขนมข้าวตังเสวยแม่ณี',
      'latitude' => '13.885542',
      'longitude' => '100.503373'
  ];
  return $messages;
}

function buttonBuild()
{
  $messages = [
      'type' => 'template',
      'altText' => 'this is a buttons template',
      'template' => [
          'type' => 'buttons',
          'thumbnailImageUrl' => 'https://example.com/bot/images/image.jpg',
          'title' => 'Menu',
          'text' => 'Please select',
          'actions' => [
                ['type' => 'message',
                'label' => 'eiei',
                'text' => 'eiei']
            ]
        ]
  ];
  return $messages;
}

function confirmBuild ($textQ,$textChoices1,$textAns1,$textChoices2,$textAns2)
{
	$messages = [
      'type' => 'template',
      'altText' => 'this is a confirm template',
      'template' => [
          'type' => 'confirm',
          'text' => $textQ,
          'actions' => [
              ['type' => 'message',
              'label' => $textChoices1,
              'text' => $textAns1],
              ['type' => 'message',
              'label' => $textChoices2,
              'text' => $textAns2]
          ]
      ]
  ];
  return $messages;
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
		'messages' => [$messages]
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
