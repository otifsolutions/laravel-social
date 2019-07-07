<?php

namespace OTIFSolutions\LaravelSocial\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Output\ConsoleOutput;

use \InstagramAPI\Instagram;

use OTIFSolutions\LaravelSocial\Models\InstaUser;
use OTIFSolutions\LaravelSocial\Models\InstaUserPost;

class AutoUpdatePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insta:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stats for top 20 posts per user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logHandler =  new ConsoleOutput;
        $this->logHandler->writeln("****************************");
        $this->logHandler->writeln("Starting Updating of Posts");
        
        $this->logHandler->writeln("Connecting to Instagram ...");
        $ig = new Instagram;
        $loginResponse = $ig->login(env('INSTAGRAM_USERNAME'), env('INSTAGRAM_PASSWORD'));
        if (!is_null($loginResponse) && $loginResponse->isTwoFactorRequired()) {
            $this->logHandler->writeln("Failed : Two factor authentication detected.");
            return;
        }
        $this->logHandler->writeln("**Connected**");
        
        $this->logHandler->writeln("Searching Pending Users");
        $users = InstaUser::where('status','=','ACTIVE')->get();
        
        foreach($users as $user){
            try{
                $instaUser = $ig->people->getInfoByName($user['username'])->getUser();
            }catch(\Exception $ex){
                $user['status'] = 'NOT_FOUND';
                $user->save();
                continue;
            }
            $user['is_private'] = $instaUser->getIsPrivate();
            $user['is_verified'] = $instaUser->getIsVerified();
            $user['media_count'] = $instaUser->getMediaCount()!= null?$instaUser->getMediaCount():0;
            $user['follower_count'] = $instaUser->getFollowerCount()!= null?$instaUser->getFollowerCount():0;
            $user['followers'] = $this->FormatNumber($user['follower_count']);
            $user['following_count'] = $instaUser->getFollowingCount()!= null?$instaUser->getFollowingCount():0;
            $user['followings'] = $this->FormatNumber($user['following_count']);
            $user['following_tag_count'] = $instaUser->getFollowingTagCount()!= null?$instaUser->getFollowingTagCount():0;
            $user['image'] = $instaUser->getProfilePicUrl();
            $user['status'] = $user['is_private']?'PRIVATE':'ACTIVE';
            $user->save();
            $this->logHandler->writeln("User Update : ".$user['full_name']);
            
            if (!$user['is_private']){
                $this->logHandler->writeln('Started User : '.$user['full_name']);
                $posts = InstaUserPost::where('insta_user_id','=',$user['id'])->orderBy('engagement',' DESC')->skip(0)->take(env('INSTAGRAM_USER_KEEP_POSTS'))->get();
                foreach($posts as $post){
                    $item = $ig->media->getInfo($post['insta_id'])->getItems()[0];
                    $post['comment_count'] = $item->getCommentCount() != null?$item->getCommentCount():0;
                    $post['comments'] = $this->FormatNumber($post['comment_count']);
                    $post['like_count'] = $item->getLikeCount() != null?$item->getLikeCount():0;
                    $post['likes'] = $this->FormatNumber($post['like_count']);
                    $post['engagement'] = $post['comment_count'] + $post['like_count'];
                    if ($post['media_type'] == 2){
                        $post['video_url'] = $item->getVideoVersions()[0]->getUrl();
                    }
                    switch ($post['media_type']){
                        case 1:
                        case 2:
                            $images = $item->getImageVersions2()->getCandidates();
                            break;
                        case 8:
                            $images = $item->getCarouselMedia()[0]->getImageVersions2()->getCandidates();
                            break;
                        default:
                            break;
                    }
                    if (isset($images[0]))
                        $post['full_image'] = $images[0]->getUrl();
                    if (isset($images[1]))
                        $post['thumb'] = $images[1]->getUrl();
                    $post->save();
                }
                $this->logHandler->writeln('Done User : '.$user['full_name']);
            }
        }
        $this->logHandler->writeln('All Done...');
        $this->logHandler->writeln("****************************");
    }
    
    private function FormatNumber($num, $precision = 1) {
        if ($num < 1000) {
            // Anything less than a million
            $numFormated = number_format($num);
        } 
        else if ($num < 1000000) {
            // Anything less than a million
            $numFormated = number_format($num / 1000,$precision) . 'K';
        } else if ($num < 1000000000) {
            // Anything less than a billion
            $numFormated = number_format($num / 1000000, $precision) . 'M';
        } else {
            // At least a billion
            $numFormated = number_format($num / 1000000000, $precision) . 'B';
        }
    
        return $numFormated;
    }
}
