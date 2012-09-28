<?php

/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */
class FD_Facebook
{
    var $app_id;
    var $app_secret;
    function FD_Facebook()
    {
        $FD = getInstance();
        $FD->loadConfig('FD_ConfigFB.php');
        $this->app_id = APP_ID_FB;
        $this->app_secret = APP_SECRET_FB;
        if(isset($_REQUEST["signed_request"])) //in facebook
        {
            $res = $this->parseSignedRequest($_REQUEST["signed_request"]);
            if(isset($res->page))
            {                
                $FD->Session->add_data(array("FB_IDFANPAGE"=>$res->page->id));
            }                
                
            if(isset($res->oauth_token)) //not loged or not installed app
            {
                $FD->Session->add_data(array("FB_VISITOR"=>$res->user_id));
                $this->setToken($res->oauth_token);
                $this->saveVisitor($res->user_id);
            }
            else
                $this->setToken('');
        }
    }

    function saveVisitor($id_user)
    {
        $FD = getInstance();
        $User = $this->getUser($id_user);
        $datas['fbid_fb_visitor'] = $User->id;
        $datas['user_fb_visitor'] = $User->name;
        $datas['email_fb_visitor'] = $User->email;
        $Ruser = $FD->Connection->DB->get_object('fb_visitor', 'fbid_fb_visitor='.$User->id);
        if(!$Ruser)
            $FD->Connection->DB->create_object('fb_visitor', $datas)->save();
    }

    function setToken($access_token)
    {
        $FD = getInstance();
        $FD->Session->add_data(array("FB_TOKEN"=>$access_token));
    }
    
    /**
     * Check the permisions for User $user_id
     * return true => if all permisions $perms was accept, false => if any permision was denegate
    **/
    function checkkPerms($perms = array(), $user_id = "me")
    {
        if(!count($perms))
            $perms = explode(',', PERMISSIONS_FB);
        $db_perms = $this->query("select ".implode(",", $perms)." from permissions where uid=$user_id");
        $band = true;
        foreach($perms as $perm)
        {
            if($db_perms["permissions"][$perm]["value"] == 0)
                $band = false;
        }
        return $band;
    }

    function isLoged()
    {
        if($this->getUser())
            return true;
        else
            return false;
    }
    
	/**
     * return current facebook user
    */
	function getUser($user_id = "me")
    {
        $FD = getInstance();
        if(!$FD->Session->get_data('FB_TOKEN'))
            return false;

    	$graph_url = "https://graph.facebook.com/$user_id?access_token=" . $FD->Session->get_data('FB_TOKEN');
	    try
        {
            $res = file_get_contents($graph_url);
            $user = json_decode($res);
            return $user;
        }catch(Exception $e)
        {
            return false;
        }
	}
	
    /**
     * check currrent facebook user if is fan of current fan page
    **/
	function checkIfUserIsFan($object_id)
    {
        $FD = getInstance();
		$graphUrl = "https://graph.facebook.com/me/likes?access_token=".$FD->Session->get_data('FB_TOKEN');
		$results = json_decode(file_get_contents($graphUrl));
		$response = false;
		$results = get_object_vars($results);
		
		if(!empty($results["data"]))
        {
			foreach ($results["data"] as $likeObj){
				
				if($likeObj->id == $object_id){
					$response = true;
					break;
				}
			}
		}
		return $response;
	}
    
    /**
     * return fan pages of current facebook user
    **/
    function getUserPages($user_id = "me")
    {
        $graph_url = '';
        $FD = getInstance();
    	$graph_url = "https://graph.facebook.com/$user_id/accounts?access_token=" . $FD->Session->get_data('FB_TOKEN');
	    $res = @json_decode(file_get_contents($graph_url));
		if($res)
            return $res->data;
        
        return array();
    }
    
    /**
     * return fan page complete info, such as page token, id, ...
    **/
    function getMyFanPage($page_id)
    {
        $Pages = $this->getUserPages();
        foreach($Pages as $page)
        {
            if($page->id == $page_id)
                return $page;
        }
    }
    
    /**
     * get the fanpage with id = $id_page
     * return fanpage info
    ***/
    function getFanPage($page_id)
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/".$page_id."?access_token=" . $FD->Session->get_data('FB_TOKEN');
        return json_decode(file_get_contents($graph_url));
    }
    
    /**
     * get a Fanpage account info
    **/
    function getFanPageAccount($pageid)
    {
        $Pages = $this->getUserPages();
        foreach($Pages as $page)
        {
            if($page->id == $pageid)
                return $page;
        }
    }
        
    /**
     * get current fanpage info
     * return a stdClass Object (    
        [algorithm] => HMAC-SHA256    
        [expires] => 0    
        [issued_at] => 1312896781    
        [oauth_token] => 223122544391265|f1a3efd92391c106b44256b0.0-100001444879309|A2wMANISsvBTTdbYQ7Zmkw_0Jvk
        [page] => stdClass Object        (            [id] => 181056671916971            [liked] => 1            [admin] => 1        )
        [user] => stdClass Object        (            [country] => bo            [locale] => en_US            [age] => stdClass Object                (                    [min] => 21                )        )
        [user_id] => 100001444879309    )
    **/
    function parseSignedRequest($signed_request = "")
    {
        if($signed_request) 
        {
            $encoded_sig = null;
            $payload = null;
            list($encoded_sig, $payload) = explode('.', $signed_request, 2);
            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
            $F = json_decode(base64_decode(strtr($payload, '-_', '+/'), true));
            //return $this->Utility->objectToArray($F);
            return $F;
        }
        return array();
    }
    
    /**
     * verify if current fb_user is admin of $id_fanpage
     **/
    function isAdmin($id_fanpage)
    {
        foreach($this->getUserPages() as $Page)
        {
            if($Page->id == $id_fanpage)
                return true;
        }
        return false;
    }
    
    /**
        $attachment =  array(
        'access_token' => $token,
        'message' => $msg,
        'name' => $title,
        'link' => $uri,
        'description' => $desc,
        'picture'=>$pic,
        'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
        );*/
    function sendFeed($datas, $to = "me")
    {
        $FD = getInstance();
        $datas["access_token"] = $FD->Session->get_data('FB_TOKEN');
        $url = "https://graph.facebook.com/$to/feed";
        $ch = curl_init();        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
        $result = curl_exec($ch);
        curl_close ($ch);
        return $result;
    }
    
    /**
     * execute the facebook query
     * return the query response
    **/
    function query($query)
    {
        $query = str_replace(" ", "+", $query);
        $FD = getInstance();
        $graph_url = "https://api.facebook.com/method/fql.query?query=$query&access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = $FD->Utility->xml2array(file_get_contents($graph_url));
        return $res["fql_query_response"];
    }
    
    
    /**
     * install current app to fanpage $page_id
     * return TRUE if is seccessfull or FALSE if had errors in the action
    */
    function installAppToPage($page_id, $app_id = null)
    {
        $Page = $this->getFanPageAccount($page_id);
        $graph_url = "https://graph.facebook.com/$page_id/tabs?app_id=".($app_id?$app_id:$this->app_id)."&method=post&access_token=".$Page->access_token;
        $res = file_get_contents($graph_url);
        return $res;
    }
    
    /**
     * Check if app installed in fanpage
     * return TRUE if app is already installed, FALSE if the app is not installed yet
    */
    function isAlreadyAppInstalled($page_id, $app_id = null)
    {
        //$Tabs = $this->getPageTabs($page_id);
        return $this->getTabFanPage($page_id, $app_id);
    }
    
    /**
     * Get all tabs from a Fan Page
     * method puede ser delete, get, post
    **/
    function getPageTabs($page_id)
    {
        $Page = $this->getFanPageAccount($page_id);
        $graph_url = "https://graph.facebook.com/$page_id/tabs?app_id=".$this->app_id."&method=GET&access_token=".$Page->access_token;	
        return json_decode(file_get_contents($graph_url));
    }
    
    /**
     * delete current tab from a Fan Page
    */    
    function deleteTabFanPage($page_id, $tab_id)
    {
        $Page = $this->getFanPageAccount($page_id);
        $graph_url = "https://graph.facebook.com/$tab_id?method=delete&access_token=".$Page->access_token;
        return json_decode(file_get_contents($graph_url));
    }
    
    /**
     * return tab information object if tab is already installed
     * return empty array if tab is not installed yet
    **/
    function getTabFanPage($page_id, $app_id = null)
    {
        $Page = $this->getFanPageAccount($page_id);
        $graph_url = "https://graph.facebook.com/$page_id/tabs/".($app_id?$app_id:$this->app_id)."?method=get&access_token=".$Page->access_token;
        $res = json_decode(file_get_contents($graph_url));
        return count($res->data)?$res->data[0]:false;
    }
    
    /**
     * update tab information
     * datas: array(position, custom_name, is_non_connection_landing_tab, custom_image_url, custom_image)
     * return boolean
     * NOTE: custom_image_url or custom_image can be set, but not both.
    **/
    function updateTabFanPage($page_id, $tab_id, $datas = array())
    {
        if(isset($datas['custom_image']))
            $datas['custom_image'] = '@'.realpath($datas['custom_image']);
            
        $tab_id = str_replace("$page_id/tabs/", '', $tab_id);
        $Page = $this->getFanPageAccount($page_id);
        $datas["access_token"] = $Page->access_token;
        $url = "https://graph.facebook.com/$page_id/tabs/$tab_id";
        $ch = curl_init();        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
        $result = json_decode(curl_exec($ch));
        curl_close ($ch);
        return $result;
    }
    
    function getPhotos($idAlbum, $user = "me")
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/$user/photos?access_token=".$FD->Session->get_data('FB_TOKEN');
        print_r(json_decode(file_get_contents($graph_url))); 
    }
    
    /**
     * return array Albums
     *      [id] => 223152484409579
            [from] => stdClass Object
                (
                    [name] => Owen Peredo D
                    [id] => 100001444879309
                )

            [name] => September 23, 2011
            [link] => http://www.facebook.com/album.php?fbid=223152484409579&id=100001444879309&aid=55724
            [privacy] => everyone
            [type] => normal
            [created_time] => 2011-09-23T12:21:09+0000
            [updated_time] => 2011-09-23T12:22:02+0000

    */
    function getAlbums($user = "me")
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/$user/albums/?access_token=".$FD->Session->get_data('FB_TOKEN');
        $Albums = json_decode(file_get_contents($graph_url));
        $Albums = $Albums->data;        
        return $Albums;
    }
    
    /**
     * create an album photo
     * return id album
    */
    function createAlbum($name, $descr = "")
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/me/albums?name=$name&message=$descr&method=post&access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = json_decode(file_get_contents($graph_url));        
        return $res->id;
    }
    
    function photoInfo($idphoto)
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/$idphoto?access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = json_decode(file_get_contents($graph_url));
        //print_r($res);        
        return $res;
    }
    
    /**
     * datas:
     * $datas['message'] = '';
       $datas['image'] = "@$img_url";
     * 
     * upload a photo in a specific gallery
     * return photo id
    */
    function uploadPhoto($datas, $album_id = "")
    {
        $Album_i = "";
        foreach($this->getAlbums() as $Album)
        {
            if($album_id && $Album->id == $album_id)
                $Album_i = $Album->id;
            elseif($Album->name == "Wall Photos" && $Album->type == "wall")
                $Album_i = $Album->id;
        }
        
        if(!$Album_i)
            $Album_i = $this->createAlbum(urlencode("Wall Photos"));

        $FD = getInstance();
        $datas["access_token"] = $FD->Session->get_data('FB_TOKEN');
        $url = "https://graph.facebook.com/$Album_i/photos/";
        $ch = curl_init();        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
        $result = json_decode(curl_exec($ch));
        curl_close ($ch);
        return $result->id;
    }
    
    /**
     * datas:
     * $datas['title'] = '';
     * $datas['description']
       $datas['file'] = "@$video_url";
     * 
     * upload a video
     * return video id
    */
    function uploadVideo($datas, $page_id)
    {
        $url = "https://graph-video.facebook.com/$page_id/videos/";
        $FD = getInstance();
        $datas["access_token"] = $FD->Session->get_data('FB_TOKEN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output
        return $result->id;
        $result = json_decode(curl_exec($ch));
        curl_close ($ch);
    }
    
    function addLike($page_id)
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/$page_id/likes?method=post&access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = json_decode(file_get_contents($graph_url));        
        return $res;
    }
    
    function removeLike($page_id)
    {
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/$page_id/likes?method=delete&access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = json_decode(file_get_contents($graph_url));        
        return $res;
    }
    
    function getFriends($user = "me()")
    {
        return $this->query("SELECT uid,name,email,pic_square FROM user WHERE uid IN (SELECT uid1 FROM friend WHERE uid2=$user) ORDER BY first_name");
    }
    
    /**
     * until, since: date
    */
    function getInsights($until = "", $since = "")
    {
        $since = "&since=$since";
        $FD = getInstance();
        $graph_url = "https://graph.facebook.com/".$this->app_id."/insights?$since&access_token=".$FD->Session->get_data('FB_TOKEN');
        $res = json_decode(file_get_contents($graph_url));        
        return $res;        
    }
}
?>