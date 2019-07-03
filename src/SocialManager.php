<?php

namespace OTIFSolutions\LaravelSocial;


use DirkGroenen\Pinterest\Pinterest;
use TwitterAPIExchange;
use PhpParser\Builder\Class_;

class SocialManager
{
    public $facebook = null;
    public $twitter = null;
    public $instagram = null;
    public $pinterest = null;

    public function __construct($facebookConfig = null, $twitterConfig = null, $instagramConfig = null, $pinterestConfig = null) {

        if ($facebookConfig)
             $this->initFacebook($facebookConfig);
        if ($twitterConfig)
            $this->initTwitter($twitterConfig);
        if ($instagramConfig)
            $this->initInstagram($instagramConfig);

        if ($pinterestConfig) {
            $this->initPinterest($pinterestConfig);
        }
    }

    public function initFacebook($config){

        $this->facebook = new class($config){

            private  $appId;
            private  $appSecret;

            public function __construct($config) {

                $this->appId = $config['app_id'];
                $this->appSecret = $config['app_secret'];

            }

            function getAllPosts($authToken, $limit = 25) {

                if ($authToken) {

                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);

                    try {
                        $response = $fb->get('/me/feed?fields=id,message,description,name,link,created_time,attachments{media}&limit='.$limit, $authToken);

                    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }
                    $posts = $response->getDecodedBody();
                    return $posts['data'];

                } else {

                    $posts = array();
                    return $posts;
                }

            }

            function createPost($pageId, $data, $accessToken) {

                try {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    return $fb->post($pageId . '/feed', $data, $accessToken);

                } catch (\Exception $e) {
                    return array(
                        'status' => 0,
                        'error_message' => 'ERROR'
                    );
                }

            }

        };
    }

    public function initTwitter($config){


        $this->twitter = new class($config){


            private  $consumerKey;
            private  $consumerSecret;

            public function __construct($config) {

                $this->consumerKey = $config['consumer_key'];
                $this->consumerSecret = $config['consumer_secret'];
            }

            function getAllPosts($details,$limit=20) {

                if ($details) {


                    /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
                    $settings = array(
                        'oauth_access_token' => $details['auth_token'],
                        'oauth_access_token_secret' => $details['secret_token'],
                        'consumer_key' => $this->consumerKey,
                        'consumer_secret' => $this->consumerSecret
                    );
                    
                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $getfield = '?screen_name=' . $details['account_email'] . '&count='.$limit;
                    $twitter = new TwitterAPIExchange($settings);
                    $posts = $twitter->setGetfield($getfield)
                        ->buildOauth($url, "GET")
                        ->performRequest();

                    $posts = json_decode($posts);

                } else
                    $posts = array();

                return $posts;

            }

            function createPost($accessToken, $accessSecret, $message, $tweetImage) {

                $twitter = new Twitter($this->consumerKey, $this->consumerSecret, $accessToken, $accessSecret);

                try {
                    if ($tweetImage != '')
                        $tweet = $twitter->send($message, $tweetImage);
                    else
                        $tweet = $twitter->send($message);

                    if ($tweet) {
                        return array(
                            'status' => 1,
                            'message' => "Successfully Posted"
                        );
                    }

                } catch (\Exception $e) {

                    return array(
                        'status' => 0,
                        'error_message' => $e->getMessage()
                    );
                }

            }

        };
    }

    public function initInstagram($config){

        $this->instagram = new class($config){

            private  $username;
            private  $password;
            private $userId;

            public function __construct($config) {

                $this->username = $config['username'];
                $this->password = $config['password'];
            }

            function getAllPosts() {

                $ig = new \InstagramAPI\Instagram();

                $ig->login($this->username, $this->password);

                return $ig->timeline->getSelfUserFeed();

            }
            
            function getSelfUser(){
                $ig = new \InstagramAPI\Instagram();
                $ig->login($this->username, $this->password);
                $this->userId = $ig->people->getUserIdForName($this->username);
                
                return $ig->people->getInfoById($this->userId)->getUser();
                
            }

            function createPost($file, $metaData, $isVideo) {


                try {
                    $ig = new \InstagramAPI\Instagram();

                    $ig->login($this->username, $this->password);

                    if ($isVideo) {

                        $video = new \InstagramAPI\Media\Video\InstagramVideo($file);
                        $ig->timeline->uploadVideo($video->getFile(), $metaData);

                    } else {

                        $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($file);
                        return $ig->timeline->uploadPhoto($photo->getFile(), $metaData);
                    }

                } catch (\Exception $e) {
                    echo 'Something went wrong: '.$e->getMessage()."\n";
                }


            }

        };
    }

    public function initPinterest($config){

        $this->pinterest = new class($config){

            private  $clientId;
            private  $clientSecret;

            public function __construct($config) {

                $this->clientId = $config['client_id'];
                $this->clientSecret = $config['client_secret'];
            }

            function getAllPins($accessToken) {

                $pinterest = new Pinterest($this->clientId, $this->clientSecret);
                $pinterest->auth->setOAuthToken($accessToken);
                return $pinterest->users->getMePins();

            }

            function createPin($accessToken, $data) {

                $pinterest = new Pinterest($this->clientId, $this->clientSecret);
                $pinterest->auth->setOAuthToken($accessToken);
                return $pinterest->pins->create($data);

            }

        };
    }

}
