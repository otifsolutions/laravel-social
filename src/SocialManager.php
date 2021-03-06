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
    public $linkedIn = null;

    public function __construct($facebookConfig = null, $twitterConfig = null, $instagramConfig = null, $pinterestConfig = null, $linkedInConfig = null)
    {
        if ($facebookConfig) {
            $this->initFacebook($facebookConfig);
        }
        if ($twitterConfig) {
            $this->initTwitter($twitterConfig);
        }
        if ($linkedInConfig) {
            $this->initLinkedIn($linkedInConfig);
        }
        if ($instagramConfig) {
            $this->initInstagram($instagramConfig);
        }
    }

    public function initFacebook($config)
    {
        $this->facebook = new class($config)
        {
            private $appId;
            private $appSecret;

            public function __construct($config)
            {
                $this->appId = $config['app_id'];
                $this->appSecret = $config['app_secret'];
            }

            function getAllPosts($authToken, $pageId, $limit = 25)
            {
                if ($authToken) {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    try {
                        $response = $fb->get('/' . $pageId . '/feed?fields=id,message,created_time,attachments{media,description},full_picture,permalink_url,likes&limit=' . $limit, $authToken);
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

            function getAllPages($authToken)
            {
                if ($authToken) {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    try {
                        $response = $fb->get('/me/accounts', $authToken);
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

            function createPost($pageId, $data, $accessToken)
            {
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

            function createImagePost($pageId, $data, $accessToken)
            {
                try {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    $upload = [
                        'message' => $data['message'],
                        'source' => $fb->fileToUpload($data['path']),
                    ];
                    return $fb->post($pageId . '/photos', $upload, $accessToken);
                } catch (\Exception $e) {
                    return array(
                        'status' => 0,
                        'error_message' => 'ERROR'
                    );
                }
            }

            function getPostsForUsername($authToken, $username, $limit = 25)
            {
                if ($authToken) {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    try {
                        $response = $fb->get('/' . $username . '/feed?fields=shares,created_time,message,story,picture,full_picture,status_type,comments.limit(0).summary(true),likes.limit(0).summary(true)&limit=' . $limit, $authToken);
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

            function getUserForUsername($authToken, $username)
            {
                if ($authToken) {
                    $fb = new \Facebook\Facebook([
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret
                    ]);
                    try {
                        $response = $fb->get('/' . $username . '?fields=about,fan_count,new_like_count,rating_count,talking_about_count,global_brand_page_name,name,name_with_location_descriptor', $authToken);
                    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }
                    return $response->getDecodedBody();
                } else {
                    $posts = array();
                    return $posts;
                }
            }
        };
    }

    public function initTwitter($config)
    {
        $this->twitter = new class($config)
        {
            private $consumerKey;
            private $consumerSecret;

            public function __construct($config)
            {
                $this->consumerKey = $config['consumer_key'];
                $this->consumerSecret = $config['consumer_secret'];
            }

            function getAllPosts($details, $limit = 20)
            {
                if ($details) {
                    /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
                    $settings = array(
                        'oauth_access_token' => $details['auth_token'],
                        'oauth_access_token_secret' => $details['secret_token'],
                        'consumer_key' => $this->consumerKey,
                        'consumer_secret' => $this->consumerSecret
                    );
                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $getfield = '?screen_name=' . $details['account_email'] . '&count=' . $limit;
                    $twitter = new TwitterAPIExchange($settings);
                    $posts = $twitter->setGetfield($getfield)
                        ->buildOauth($url, "GET")
                        ->performRequest();
                    $posts = json_decode($posts);
                } else
                    $posts = array();
                    return $posts;
            }

            function createPost($accessToken, $accessSecret, $message, $imagePath = null)
            {
                $settings = array(
                    'oauth_access_token' => $accessToken,
                    'oauth_access_token_secret' => $accessSecret,
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret
                );
                $url = "https://api.twitter.com/1.1/statuses/update.json";
                $postFields = [
                    'status' => $message,
                    'skip_status' => '1'
                ];
                $twitter = new TwitterAPIExchange($settings);
                if ($imagePath !== null) {
                    $response = $twitter->setPostfields([
                        'media_data' => base64_encode(file_get_contents($imagePath))
                    ])
                        ->buildOauth('https://upload.twitter.com/1.1/media/upload.json', "POST")
                        ->performRequest();
                    $response = json_decode($response);
                    $postFields['media_ids'] = $response->media_id_string;
                }
                $response = $twitter->setPostfields($postFields)
                    ->buildOauth($url, "POST")
                    ->performRequest();
                return json_decode($response);
            }

            function getUserForUsername($details)
            {
                if ($details) {
                    $settings = array(
                        'oauth_access_token' => $details['auth_token'],
                        'oauth_access_token_secret' => $details['secret_token'],
                        'consumer_key' => $this->consumerKey,
                        'consumer_secret' => $this->consumerSecret
                    );
                    $url = "https://api.twitter.com/1.1/users/show.json";
                    $getfield = '?screen_name=' . $details['username'];
                    $twitter = new TwitterAPIExchange($settings);
                    $response = $twitter->setGetfield($getfield)
                        ->buildOauth($url, "GET")
                        ->performRequest();
                    return json_decode($response);
                } else return null;
            }

            function getTweetsForUsername($details)
            {
                if ($details) {
                    $settings = array(
                        'oauth_access_token' => $details['auth_token'],
                        'oauth_access_token_secret' => $details['secret_token'],
                        'consumer_key' => $this->consumerKey,
                        'consumer_secret' => $this->consumerSecret
                    );
                    $count = isset($details['count']) ? $details['count'] : '200';
                    $max = isset($details['max_id']) ? '&max_id=' . $details['max_id'] : '';
                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $getfield = '?screen_name=' . $details['username'] . '&count=' . $count . $max;
                    $twitter = new TwitterAPIExchange($settings);
                    $response = $twitter->setGetfield($getfield)
                        ->buildOauth($url, "GET")
                        ->performRequest();
                    return json_decode($response);
                } else return null;
            }
        };
    }

    public function initLinkedIn($config)
    {
        $this->linkedIn = new class($config)
        {
            private $clientId;
            private $clientSecret;

            public function __construct($config = null)
            {
                if ($config) {
                    $this->clientId = $config['client_id'];
                    $this->clientSecret = $config['client_secret'];
                }
            }

            function getSelfUser($accessToken)
            {
                $token = new \League\OAuth2\Client\Token\AccessToken(['access_token' => $accessToken]);
                $linkedIn = new \League\OAuth2\Client\Provider\LinkedIn();
                return $linkedIn->withFields(['id', 'firstName', 'lastName', 'maidenName', 'headline', 'vanityName', 'profilePicture'])->getResourceOwner($token);
            }

            function createPost($accessToken, $userId, $data, $mode='personal')
            {
                if (!isset($accessToken) || !isset($data)) return null;
                if($mode=='personal')
                    $userId = 'urn:li:person:'.$userId;
                $client = new \GuzzleHttp\Client();
                $postData['headers'] = [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ];
                $postData['json'] = [
                    'owner' => $userId,
                    'subject' => $data['title'],
                    'text' => ['text' => $data['content']],
                    'distribution' => [
                        'linkedInDistributionTarget' => [
                            'visibleToGuest' => true
                            ]
                        ]
                ];
                $postData['json']['content'] = [
                    'title' => $data['title']
                ];
                if (!empty($data['image_url']) && !is_null($data['image_url'])) {
                    $postData['json']['content']['contentEntities'] = [
                        [
                            'entityLocation' => $data['image_url'],
                            'thumbnails' => [
                                [
                                    'resolvedUrl' => $data['image_url']
                                ]
                            ]
                        ]
                    ];
                }
                $response = $client->post('https://api.linkedin.com/v2/shares', $postData);
                return $response;
            }

            function getCompanies($accessToken){
                if (!isset($accessToken)) return null;
                $client = new \GuzzleHttp\Client();

                $response = $client->get('https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee&role=ADMINISTRATOR&projection=(elements*(organizationalTarget~(localizedName,vanityName,logoV2)))',[
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json'
                    ]
                ]);
                return json_decode($response->getBody());
            }
        };
    }
    public function initInstagram($config)
    {
        $this->instagram = new class($config) {

            private $clientId;
            private $clientSecret;

            public function __construct($config = null)
            {
                if ($config) {
                    $this->clientId = $config['client_id'];
                    $this->clientSecret = $config['client_secret'];
                }
            }

            function getInstagramMedia($token)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://graph.instagram.com/me/media?fields=id,caption,media_url,media_type,username,permalink,thumbnail_url,timestamp&access_token=" . $token,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return json_decode($response, true);
            }
        };
    }
}
