<?php 
////////////////////////////////////////////////////////////////////////////
	// Download Youtube Playlist
	/**
	 * Simple script for youtube playlist download
	 * Offliberty package needed https://www.npmjs.com/package/offliberty
	 **/
//////////////////////////////////////////////////////////////////////////


// Use this file to write video urls
const myFile = 'music1.txt';

// Use this file to write download Urls from Offliberty 
const myDownloadFile = 'musicDownload.txt';

// Set playlist url
const Playlist_URL = 'https://www.youtube.com/playlist?list=PLH6eNVHVEetB-8oOWaXBHBewhOfLz_uRf';
DownloadYoutube();

function DownloadYoutube()
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_URL, Playlist_URL);		
	$strResult = curl_exec($ch);
	$inthttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	preg_match_all('%(?<=<td class="pl-video-thumbnail">)(.*?)(class="ux-thumb-wrap yt-uix-sessionlink contains-addto pl-video-thumb")%', $strResult, $arrlist);
	if($inthttpCode==200)
	{
		$intListSize = sizeof($arrlist[1]);
		$i = 0;
		$fileLinks = fopen(myFile, 'w') or die("can't open file");
		for($i;$i<$intListSize;$i++)
		{
			// parse link
			$arrExplode = explode('<a href="', $arrlist[1][$i]);
			$arrExplodeF = explode('&amp;',$arrExplode[1]);
	
			// write video url to file
			fwrite($fileLinks, 'http://youtube.com'.$arrExplodeF[0]."\n");
		}
		fclose($fileLinks);
		// Get mp3 download links
		$output = shell_exec('off '.myFile);
		// open file
		$fileDownloadLinks = fopen(myDownloadFile, 'w') or die("can't open file");
		// write links to file 
		fwrite($fileDownloadLinks, $output);
		fclose($fileDownloadLinks);
		ParseDownloadFile();
	}
	else
	{
	echo "Server error \n";
	}
}

function ParseDownloadFile()
{
	$fileDownloadLinks = fopen(myDownloadFile, 'r') or die("can't open file");

	if(filesize(myDownloadFile)>0)
	{
	  $contents = fread($fileDownloadLinks, filesize(myDownloadFile));
	  $arrDlinks = explode("\n", $contents);
	}
	else
	{
		echo "Empty file";
		return false;
	}

	foreach ($arrDlinks as  $value)
	{
		// Check valid url
		$boolCheckURL = preg_match("/^(http?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/", $value);
		if($boolCheckURL)
		{
			DownloadMp3($value);
		}
		else
		{
			echo "Offliberty script returned null \n";
		}
	}
}

function DownloadMp3($strUrl)
{
	echo $strUrl;
	// TODO Download files
}
?>