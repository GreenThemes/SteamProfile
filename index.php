<?php
/*

Author : LoveHoly

*/
class SteamProfile 
{
	public $Image;
	public $GetCustomURL;
	public $GetImage;
	public $GetProfile;
	public $FileImage; 
	public $AvatarImage;
	public $ColorGrayBorder;
	public $ColorGray;
	public $ColorOnline;
	public $ColorInGame;
	public $ColorpOnline;
	public $ColorpInGame;
	public $ColorStatus;
	
	// init class
	public function __construct()
	{
		$this->GetCustomURL = urlencode($_GET['id']);
		if(!$this->GetCustomURL) $this->GetCustomURL = "LoveHoly";
		switch($_GET['image'])
		{
			case 0:
				$this->GetImage="base";
				break;
			case 1:
				$this->GetImage="base";
				break;
			default:
				$this->GetImage="base";
				break;				
		}
		$this->GetProfile = simplexml_load_string(file_get_contents("http://steamcommunity.com/id/".$this->GetCustomURL."/?xml=1"));
		
		$this->FileImage = ImageCreateFromPNG("images/".$this->GetImage.".png"); 
		$this->AvatarImage = ImageCreateFromJPEG($this->GetProfile->avatarMedium); 
		
		$this->ColorGrayBorder = imagecolorallocate($this->FileImage, 0, 0, 0);
		$this->ColorGray = imagecolorallocate($this->FileImage, 255, 255, 255);
		$this->ColorOnline = imagecolorallocate($this->FileImage, 128, 181, 223);
		$this->ColorInGame = imagecolorallocate($this->FileImage, 167, 212, 105);
		$this->ColorpOnline = imagecolorallocate($this->FileImage, 255-128, 255-181, 255-223);
		$this->ColorpInGame = imagecolorallocate($this->FileImage, 255-167, 255-212, 255-105);
		
		@imagecopymerge($this->FileImage, $this->AvatarImage, 10, 10, 0, 0, 64, 64, 100); 
		
		$this->ImageTTFStrokeText($this->FileImage, 12, 0, 84, 24, $this->ColorGray,$this->ColorGrayBorder, "NanumGothic.ttf", $this->GetProfile->realname,1);
		
		if($this->GetProfile->onlineState == "in-game") 
		{ 
			$ColorStatus = $this->ColorInGame; $ColorpStatus = $this->ColorpInGame; $TextStatus = "게임 중"; 
		}
		elseif($this->GetProfile->onlineState == "online")
		{ 
			$ColorStatus = $this->ColorOnline; $ColorpStatus = $this->ColorpOnline; $TextStatus = "온라인"; 
		}
		elseif($this->GetProfile->onlineState == "offline") 
		{ 
			$ColorStatus = $this->ColorGray;$ColorpStatus = $this->ColorGrayBorder; $TextStatus = "오프라인"; 
		}
		else 
		{
			$ColorStatus = $this->ColorOnline; $ColorpStatus = $this->ColorpOnline; $TextStatus = $GetProfile->stateMessage;
		}
		
		$this->ImageTTFStrokeText($this->FileImage, 9, 0, 84, 44, $ColorStatus,$ColorpStatus, "NanumGothic.ttf", $TextStatus,1);
		$this->ImageTTFStrokeText($this->FileImage, 9, 0, 84, 62, $this->ColorGray,$this->ColorGrayBorder, "NanumGothic.ttf", $this->GetProfile->location,1);
		
		$this->ImageTTFStrokeText($this->FileImage, 8, 0, 14, 102, $this->ColorGray,$this->ColorGrayBorder, "NanumGothic.ttf", "자주 하는 게임",1);
		$this->ImageTTFStrokeText($this->FileImage, 8, 0, 244, 102, $this->ColorGray,$this->ColorGrayBorder, "NanumGothic.ttf", "많이 플레이 한 게임",1);
		
		$TmpImage = @ImageCreateFromJPEG($this->GetProfile->mostPlayedGames->mostPlayedGame[0]->gameIcon); 
		@imagecopymerge($this->FileImage, $TmpImage, 14, 112, 0, 0, 32, 32, 100); 
		@ImageDestroy($TmpImage); 
		
		$this->ImageTTFStrokeText($this->FileImage, 8, 0, 54, 122, $this->ColorGray,$this->ColorGrayBorder, "NanumGothic.ttf", $this->GetProfile->mostPlayedGames->mostPlayedGame[0]->gameName,1);
		///@imagettftext($FileImage, 8, 0, 254, 40, $ColorGray, "NanumGothic.ttf", $GetProfile->mostPlayedGames->mostPlayedGame[0]->gameName);
		$this->ImageTTFStrokeText($this->FileImage, 8, 0, 54, 140, $this->ColorGray, $this->ColorGrayBorder, "NanumGothic.ttf", "총 ".$this->GetProfile->mostPlayedGames->mostPlayedGame[0]->hoursOnRecord."시간 플레이",1);
	
		if($this->GetProfile->mostPlayedGames->mostPlayedGame[1]->gameIcon)
		{
			$TmpImage = @ImageCreateFromJPEG($this->GetProfile->mostPlayedGames->mostPlayedGame[1]->gameIcon); 
			@imagecopymerge($this->FileImage, $TmpImage, 244, 112, 0, 0, 32, 32, 100); 
			@ImageDestroy($TmpImage); 
		}
		if($this->GetProfile->mostPlayedGames->mostPlayedGame[2]->gameIcon)
		{
			$TmpImage = @ImageCreateFromJPEG($this->GetProfile->mostPlayedGames->mostPlayedGame[2]->gameIcon); 
			@imagecopymerge($this->FileImage, $TmpImage, 284, 112, 0, 0, 32, 32, 100); 
			@ImageDestroy($TmpImage); 
		}
		if($this->GetProfile->mostPlayedGames->mostPlayedGame[3]->gameIcon)
		{
			$TmpImage = @ImageCreateFromJPEG($this->GetProfile->mostPlayedGames->mostPlayedGame[3]->gameIcon); 
			@imagecopymerge($this->FileImage, $TmpImage, 324, 112, 0, 0, 32, 32, 100); 
			@ImageDestroy($TmpImage); 
		}
		
	}
	
	public function __destruct(){
		imagePng($this->FileImage); 
		ImageDestroy($this->AvatarImage); 
		ImageDestroy($this->FileImage); 
	}
	
	function ImageTTFStrokeText(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
		$AbsCache = abs($px);
		for($c1 = ($x-$AbsCache); $c1 <= ($x+$AbsCache); $c1++)
			for($c2 = ($y-$AbsCache); $c2 <= ($y+$AbsCache); $c2++)
				$bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
	 
	   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
	}
	
	function ImageColorAllocatieReverse($a,$b,$c,$d)
	{
		return imagecolorallocate($a, 255-$b, 255-$c, 255-$d);
	}
	
}
Header("Content-Type: image/png"); 
$SP = new SteamProfile();