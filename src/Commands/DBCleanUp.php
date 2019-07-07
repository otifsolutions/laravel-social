<?php

namespace OTIFSolutions\LaravelSocial\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Output\ConsoleOutput;

use OTIFSolutions\LaravelSocial\Models\InstaUser;
use OTIFSolutions\LaravelSocial\Models\InstaUserPost;

class DBCleanUp extends Command
{
    
    private $logHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:insta:cleanup';

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
        $date = new \DateTime('-'.env('INSTAGRAM_USER_KEEP_DAYS').' days');
        $users = InstaUser::where('last_viewed_at','<=',$date->format('Y-m-d'))->get();
        foreach($users as $user)
        {
            $ids = InstaUserPost::where('insta_user_id','=',$user['id'])->pluck('id');
            InstaUserPost::whereIn('id', $ids)->delete();
            $user->delete();
        }
        $users = InstaUser::all();
        foreach($users as $user){
            switch($user['status']){
                case 'ACTIVE':
                    $ids = InstaUserPost::where('insta_user_id','=',$user['id'])->orderBy('engagement',' DESC')->skip(env('INSTAGRAM_USER_KEEP_POSTS'))->take((env('INSTAGRAM_FETCH_LIMIT') + 100))->pluck('id');
                    InstaUserPost::whereIn('id', $ids)->delete();
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
