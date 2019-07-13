<?php

namespace OTIFSolutions\LaravelSocial\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Output\ConsoleOutput;

use OTIFSolutions\LaravelSocial\Models\InstaUser;
use OTIFSolutions\LaravelSocial\Models\TwitterUser;
use OTIFSolutions\LaravelSocial\Models\FacebookUser;

class DBCleanUp extends Command
{
    
    private $logHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep Top Posts for each user and remove all others.';

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
        $this->logHandler->writeln("Started Cleanup of DB.");
        $this->logHandler->writeln("Fetching Users from DB");
        $date = new \DateTime('-'.env('SOCIAL_USER_KEEP_DAYS').' days');
        
        $this->deleteUsers(InstaUser::where('last_viewed_at','<=',$date->format('Y-m-d'))->get());
        $this->cleanPostsForUsers(InstaUser::all());
        $this->deleteUsers(TwitterUser::where('last_viewed_at','<=',$date->format('Y-m-d'))->get());
        $this->cleanPostsForUsers(TwitterUser::all());
        $this->deleteUsers(FacebookUser::where('last_viewed_at','<=',$date->format('Y-m-d'))->get());
        $this->cleanPostsForUsers(FacebookUser::all());
        
    }
    
    private function deleteUsers($users){
        foreach($users as $user)
        {
            $user->posts()->delete();
            $user->delete();
        }
    }
    private function cleanPostsForUsers($users){
        foreach($users as $user){
            switch($user['status']){
                case 'ACTIVE':
                    $user->posts()->orderBy('engagement','DESC')->skip(env('SOCIAL_USER_KEEP_POSTS'))->take((env('SOCIAL_FETCH_LIMIT') + 100))->delete();
                    break;
                case 'PRIVATE':
                case 'NOT FOUND':
                case 'PENDING':
                default:
                    break;
            }
            $this->logHandler->writeln("Done User : ".$user['full_name']);
        }
    }
}
