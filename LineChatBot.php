<?php
//Connect PostageDB
// $host = 'ec2-54-221-254-72.compute-1.amazonaws.com';
// $dbname = 'de6sfosesim5hp';
// $user = 'sicngsjfdewwql';
// $pass = 'b5cf4b4612c0625c4e9ce261c84939a5bb33bf66dd5a95cd12b5fc1792019b6d';
// $connention = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);

//Connect Azure DB
$connention = new PDO("sqlsrv:server = tcp:preprojectlinebot.database.windows.net,1433; Database = linebotDB", "supanun", "l64koyoBABE");
$connention->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				while($rs = $result->fetch())
				{
					pushMessage($userID,textBuild('มันไม่เป็นNULLเว้ย'),$access_token);
					pushMessage($userID,textBuild($rs['line_id']),$access_token);
				}
				pushMessage($userID,textBuild($userID),$access_token);
				//$textquery = sprintf("%s",$result);
				/*if($result!=null)
				{
					pushMessage($userID,textBuild('มันไม่เป็นNULLเว้ย'),$access_token);
					pushMessage($userID,textBuild($result->rowCount()),$access_token);
					//pushMessage($userID,textBuild($textquery),$access_token);
				}*/
			}
			if ($getText=='สมัครสมาชิก')
			{
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'ในการสมัครสมาชิกคุณจะต้องกรอกข้อมูลตามจริงเพื่อรักษาสิทธิประโยชน์ของคุณ ท่านต้องการสมัครสมาชิกหรือไม่';
					pushMessage($userID,confirmBuild($con_title,uriAction('ต้องการ','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการ','ไม่ต้องการ')),$access_token);
				}
				if($user_check)
				{
					pushMessage($userID,textBuild('คุณไม่สามารถสมัครสมาชิกได้เนื่องจากคุณเป็นสมาชิกอยู่แล้ว'),$access_token);
				}
			}
			else if (strpos($getText,"สมัครสมาชิก,")!==false)
			{
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$text = str_replace('สมัครสมาชิก','',$getText);
					$register = explode(',',$text);
					$iCount = count($register);
					$inform = ['ชื่อ','นามสกุล','เบอร์โทรศัพท์ที่สามารถติดต่อได้','บ้านเลขที่','หมู่บ้าน','ซอย','ถนน','แขวง','อำเภอ','จังหวัด','รหัสไปรษณีย์','ข้อมูลอื่นๆ'];
					$ansText = '';
					for ($i = 0; $i<$iCount-1; $i++)
					{
						$ansText = $ansText.'   '.$inform[$i] . ' : ' . $register[$i+1];
					}
					$text1 = $ansText;
	        pushMessage($userID,textBuild($text1),$access_token);
	        pushMessage($userID,textBuild('คุณได้ทำการสมัครสมาชิกเรียบร้อยแล้ว คุณสามารถใช้งานบริการต่างๆได้ทันที'),$access_token);
					//Query INSERT REGISTER
					$statement = $connention->prepare('INSERT INTO customer (line_id, u_name,u_lastname,u_status,u_tel,house_no,village,lane,road,subarea,area,province,postal_code,annotation) VALUES (:line_id, :u_name, :u_lastname, :u_status, :u_tel, :house_no, :village, :lane, :road, :subarea, :area, :province, :postal_code, :annotation)');
					$statement->execute(array(
						'line_id' => $userID,
						'u_name' => $register[1],
						'u_lastname' => $register[2],
						'u_status' => 1,
						'u_tel' => $register[3],
						'house_no' => $register[4],
						'village' => $register[5],
						'lane' => $register[6],
						'road' => $register[7],
						'subarea' => $register[8],
						'area' => $register[9],
						'province' => $register[10],
						'postal_code' => $register[11],
						'annotation' => $register[12]
					));
				}
				if($user_check)
				{
					pushMessage($userID,textBuild('คุณไม่สามารถสมัครสมาชิกได้เนื่องจากคุณเป็นสมาชิกอยู่แล้ว'),$access_token);
				}
			}
			else if ($getText=='ดูเมนูและสั่งซื้อสินค้า'||$getText=='ดูเมนู'||$getText=='สั่งซื้อ')
			{
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการ','ไม่ต้องการ')),$access_token);
				}
				if($user_check)
				{
					$result = $connention->prepare("SELECT * FROM product");
					$text = $result->execute();
					$i=0;
					$menu_name;
					$menu_description;
					$menu_image;
					while($rs = $result->fetch())
					{
						$menu_name[$i] = $rs['p_name'];
						$menu_description[$i] = $rs['p_description'];
						$menu_image[$i] = 'https://preproject2mvc20171126124543.azurewebsites.net/BG/'.$rs['p_image'];
						$i++;
					}
					$columnMenu;
					for ($j=0; $j < count($menu_name) ; $j++)
					{
						$columnMenu[$j] = columnBuild($menu_name[$j],$menu_image[$j],$menu_description[$j],uriAction('สั่งซื้อ','http://webforlinechat.azurewebsites.net/Product/ShopProduct/'.$userID));
					}
					pushMessage($userID,carouselBuild($columnMenu),$access_token);
				}
			}
			else if ($getText=='ดูข้อมูลร้านค้า')
			{
				$imageOrigi ='https://scontent-kut2-1.xx.fbcdn.net/v/t1.0-9/22814405_1701979389873235_1671810247852454191_n.jpg?oh=53a9387cb9c73b3ccf62a3534d62e6b0&oe=5A782E36';
				$imagePreview ='https://scontent-kut2-1.xx.fbcdn.net/v/t1.0-9/22814405_1701979389873235_1671810247852454191_n.jpg?oh=53a9387cb9c73b3ccf62a3534d62e6b0&oe=5A782E36';
				pushMessage($userID,imageBuild($imageOrigi,$imagePreview),$access_token);
				pushMessage($userID,textBuild('ขนมข้าวตังเสวยเป็นขนมโบราณหากินได้อยาก มีรสชาติแสนอร่อย ด้วยสูตรลับของท่านร้านทำให้ขนมมีความกรอบ หอมหวาน กลมกล่อม นึกถึงขนมข้าวตังเสวยต้องนึกถึงร้านเมณีเท่านั้น!'),$access_token);
				pushMessage($userID,textBuild('ท่านสามารถติดต่อทางร้านได้โดยช่องทางดังนี้'),$access_token);
				pushMessage($userID,locationBuild(),$access_token);
        pushMessage($userID,textBuild('เบอรโทรศัพท์ 0817349462 และ 0818178962'),$access_token);
			}
			else if ($getText=='ตรวจสอบสถานะออเดอร์') {
				$result = $connention-prepare("SELECT * FROM order_list WHERE line_id=:line_id");
				$result->bindParam(':line_id',$userID,PDO::FETCH_ASSOC);
				$result->execute();
				$text = '';
				// while ($ob = $result->fetchObject())
				// {
				// 	if($ob.['o_status'] == 0)
				// 	{
				// 		$text += 'รหัสออเดอร์ : '.$ob['o_id'].'   วันที่ : '.$ob['o_date'].'   สถานะออเดอร์ : ยังไม่โอน   ราคา : '.$ob['total_price'];
				// 	}
				// 	else if ($ob.['o_status'] == 1)
				// 	{
				// 		$text += 'รหัสออเดอร์ : '.$ob['o_id'].'   วันที่ : '.$ob['o_date'].'   สถานะออเดอร์ : โอนแล้ว   ราคา : '.$ob['total_price'];
				// 	}
				// 	else if ($ob.['o_status'] == 2)
				// 	{
				// 		$text += 'รหัสออเดอร์ : '.$ob['o_id'].'   วันที่ : '.$ob['o_date'].'   สถานะออเดอร์ : กำลังจัดส่ง   ราคา : '.$ob['total_price'];
				// 	}
				// 	pushMessage($userID,textBuild($text),$access_token);
				// }
				$ob = $result->fetchObject();
				pushMessage($userID,textBuild($ob->rowCount()),$access_token);

			}
			else if ($getText=='ดูข้อมูลส่วนตัว')
			{
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$result = $connention->prepare("SELECT * FROM customer WHERE line_id = :line_id");
					$result->bindParam(':line_id',$userID,PDO::FETCH_ASSOC);
					$result->execute();
					$ob = $result->fetchObject();
					if($ob->u_status==1) $u_status = 'ปกติ';
					else if ($ob->u_status==0) $u_status = 'ถูกระงับการใช้งาน';
					$textResult = 'ชื่อ : '.$ob->u_name.'   นามสกุล : '.$ob->u_lastname.'   เบอร์โทรศัพท์ : '.$ob->u_tel.'   สถานะผู้ใช้ : '.$u_status;
					$textResult2 = 'บ้านเลขที่ : '.$ob->house_no.'   หมู่บ้าน : '.$ob->village.'   ซอย : '.$ob->lane.'   ถนน : '.$ob->road.'   ตำบล : '.$ob->subarea.'   อำเภอ : '.$ob->area.'   จังหวัด : '.$ob->province.'   รหัสไปรษณีย์ : '.$ob->postal_code;
					$textResult3 = 'หมายเหตุ : '.$ob->annotation;
					pushMessage($userID,textBuild($textResult),$access_token);
					pushMessage($userID,textBuild($textResult2),$access_token);
					pushMessage($userID,textBuild($textResult3),$access_token);
					pushMessage($userID,confirmBuild ('คุณต้องการแก้ไขข้อมูลส่วนตัวของคุณหรือไม่',messageAction('ต้องการ','ฉันต้องการแก้ไขข้อมูล'),messageAction('ไม่ต้องการ','ฉันไม่ต้องการแก้ไขข้อมูล')),$access_token);
				}

				// pushMessage($userID,textBuild('eiei'),$access_token);
				// pushMessage($userID,textBuild('มันไม่เป็นNULLเว้ย'),$access_token);
				// pushMessage($userID,textBuild($rs['line_id']),$access_token);
				// pushMessage($userID,textBuild($rs['u_name']),$access_token);
        //
				// pushMessage($userID,textBuild('ข้อมูลของคุณคือ'),$access_token);
				 // pushMessage($userID,confirmBuild('คุณต้องการแก้ไขข้อมูลส่วนตัวของคุณหรือไม่','ต้องการ','ฉันต้องการแก้ไขข้อมูล','ไม่ต้องการ','ฉันไม่ต้องการแก้ไขข้อมูล'),$access_token);

			}
			else if ($getText=='ฉันต้องการแก้ไขข้อมูล')
			{
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					pushMessage($userID,textBuild('หากคุณต้องการแก้ไขข้อมูลส่วนตัวของคุณ กรุณาพิมพ์ตามรูปแบบการแก้ไขดังนี้'),$access_token);
	        pushMessage($userID,textBuild('แก้ไข,สิ่งที่คุณต้องการแก้ไข,ข้อมูลที่แก้ไขแล้ว เช่น คุณต้องการแก้ไขเบอร์โทรศัพท์ จะต้องพิมพ์ดังนี้ แก้ไข,เบอร์,0812345678 เป็นต้น'),$access_token);
					pushMessage($userID,textBuild('ข้อมูลที่ท่านสามารถแก้ไขได้มีดังนี้ ชื่อ,นามสกุล,เบอร์,บ้านเลขที่,ซอย,หมู่บ้าน,แขวง,อำเภอ,จังหวัด,รหัสไปรษณีย์,ข้อมูลอื่นๆ'),$access_token);
				}
			}
			else if (strpos($getText,'แก้ไข,ชื่อ')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET u_name=:u_name WHERE line_id=:line_id');
					$statement->execute(array(
						'u_name' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,นามสกุล')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET u_lastname=:u_lastname WHERE line_id=:line_id');
					$statement->execute(array(
						'u_lastname' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,เบอร์โทรศัพท์')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET u_tel=:u_tel WHERE line_id=:line_id');
					$statement->execute(array(
						'u_tel' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,บ้านเลขที่')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET house_no=:house_no WHERE line_id=:line_id');
					$statement->execute(array(
						'house_no' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,หมู่บ้าน')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET village=:village WHERE line_id=:line_id');
					$statement->execute(array(
						'village' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,ซอย')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET lane=:lane WHERE line_id=:line_id');
					$statement->execute(array(
						'lane' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,ถนน')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET road=:road WHERE line_id=:line_id');
					$statement->execute(array(
						'road' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,ตำบล')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET subarea=:subarea WHERE line_id=:line_id');
					$statement->execute(array(
						'subarea' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,อำเภอ')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET area=:area WHERE line_id=:line_id');
					$statement->execute(array(
						'area' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,จังหวัด')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET province=:province WHERE line_id=:line_id');
					$statement->execute(array(
						'province' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,รหัสไปรษนีย์')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET postal_code=:postal_code WHERE line_id=:line_id');
					$statement->execute(array(
						'postal_code' => $register[2],
						'line_id' => $userID
					));
				}
			}
			else if (strpos($getText,'แก้ไข,หมายเหตุ')!==false)
			{
				$text = str_replace('แก้ไข','',$getText);
				$register = explode(',',$text);
				$result = $connention->prepare("SELECT * FROM customer");
				$text = $result->execute();
				$user_check = false;
				while($rs = $result->fetch())
				{
					if($userID==$rs['line_id'])
					{
						$user_check = true;
					}
				}
				if(!$user_check)
				{
					$con_title = 'คุณไม่สามารถใช้บริการนี้ได้ กรุณาสมัครสมาชิก';
					pushMessage($userID,confirmBuild($con_title,uriAction('สมัครสมาชิก','http://webforlinechat.azurewebsites.net//Regis/Index/'.$userID),messageAction('ไม่ต้องการสมัคร','ไม่ต้องการสมัคร')),$access_token);
				}
				if($user_check)
				{
					$statement = $connention->prepare('UPDATE customer SET annotation=:annotation WHERE line_id=:line_id');
					$statement->execute(array(
						'annotation' => $register[2],
						'line_id' => $userID
					));
				}
			}
		}
    if ($event['type'] == 'message' && $event['message']['type'] == 'sticker')
    {
      $userID = $event['source']['userId'];
      pushMessage($userID,stickerBuild(),$access_token);
    }
	}
}
///////////////////////////////////Build Actions/////////////////////////////////////////////////////////////////////////
function messageAction ($textLabel,$textAns)
{
	$action = [
		'type' => 'message',
		'label' => $textLabel,
		'text' => $textAns
	];
	return $action;
}
function uriAction($textLabel,$textUri)
{
	$action = [
		'type' => 'uri',
		 'label' => $textLabel,
		 'uri' => $textUri
	];
	return $action;
}
//////////////////////////////////Build messages///////////////////////////////////////////////////////////////////////////
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
      'stickerId' => rand(1,10)
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
///////////////////////////////////////template//////////////////////////////////////////////////////////////////////////
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
function confirmBuild ($textQ,$action1,$action2)
{
	$messages = [
      'type' => 'template',
      'altText' => 'this is a confirm template',
      'template' => [
          'type' => 'confirm',
          'text' => $textQ,
          'actions' => [$action1,$action2]
      ]
  ];
  return $messages;
}
function columnBuild($title,$linkPic,$description,$actions)
{
	$columns = [
		'thumbnailImageUrl' => $linkPic,
		 'title' => $title,
		 'text' => $description,
		 'actions' => [$actions]
	];
	return $columns;
}
function carouselBuild($columns)
{
	$messages = [
		'type' => 'template',
		'altText' => 'this is a carousel template',
		'template' => [
			'type' => 'carousel',
			'columns' => $columns
		]
	];
	return $messages;
}

function testcarouselBuild()
{
	$messages = [
		'type' => 'template',
		'altText' => 'this is a carousel template',
		'template' => [
			'type' => 'carousel',
			'columns' => [
				['thumbnailImageUrl' => 'https://example.com/bot/images/item1.jpg',
			   'title' => 'this is menu',
			   'text' => 'description',
			   'actions' => [
					 ['type' => 'message',
				    'label' => 'buy1',
					  'text' => 'buy1'],
					 ['type' => 'message',
				    'label' => 'buy2',
					  'text' => 'buy2'],
					 ['type' => 'uri',
				    'label' => 'buy3',
					  'uri' => 'https://www.youtube.com/']]],
				 ['thumbnailImageUrl' => 'https://example.com/bot/images/item2.jpg',
					 'title' => 'this is menu',
	  				'text' => 'description',
	  				'actions' => [
	  					['type' => 'message',
	  					 'label' => 'eiei1',
	  					 'text' => 'eiei1'],
	  					['type' => 'message',
	  					 'label' => 'eiei2',
	  					 'text' => 'eiei2'],
	  					['type' => 'message',
	  					 'label' => 'eiei3',
	  					 'text' => 'eiei3']]]
			]
		]
	];
	return $messages;
}
//////////////////////////////////send messages//////////////////////////////////////////////////////////////////////////////
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
