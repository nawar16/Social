<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use DB;
use Validator;
use Storage;
use File;
use Redirect;
use Response;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserFollowed as UserFollowed;
use Intervention\Image\Facades\Image as Image;

class UsersController extends Controller
{
    public $loginAfterSignUp = true;
    
    public function __construct()
    {
      $this->middleware('auth:web', ['except' => ['dashboard','Login','Register']]);
      $this->guard = "web";
    }    

    // --------------------- [ Register user ] ----------------------
    public function Register(Request $request) {

        // validate form fields
        $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

        $input = $request->all();

        // if validation success then create an input array
        $inputArray= array(
            'name'  =>  $request->name,
            'email'  =>  $request->email,
            'password' => Hash::make($request->password),
        );
        // register user
        $user = User::create($inputArray);
        
        // if registration success then return with success message
        if(!is_null($user)) {
            Auth::login($user);
            return redirect()->intended('/');
        }
        // else return with error message
        else {
            return redirect()->back()->with('message', 'Whoops! some error encountered. Please try again.');
        }
    }

    // --------------------- [ User login ] ---------------------
    public function Login(Request $request) {

        $this->validate($request, [
            "email"           =>    "required|email",
            "password"        =>    "required|min:6"
        ]);

        $userCredentials = $request->only('email', 'password');

        // check user using auth function
        if (Auth::attempt($userCredentials)) {
            return redirect()->intended('/');
        }
        else {
            return redirect()->back()->with('message', 'Whoops! invalid username or password.');
        }
    }


    // ------------------ [ User Dashboard Section ] ---------------------
    public function dashboard() {

        $posts = Post::latest()->paginate(10);
        // check if user logged in
        if(Auth::check()) {
            return view('users.dashboard', compact('posts'));
        }

        return redirect::to("/login")->with('Oopps! You should have an account');
    }


    // ------------------- [ User logout function ] ----------------------
    public function logout(Request $request ) {
        $request->session()->flush();
        Auth::logout();
        return Redirect('/login');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    //////users//////
    public function index()
    {
        $i = auth()->user()->id;
        $users = User::where('users.id', '!=', $i)->get();

        return view('users.index', compact('users'));
    }
    public function follow(User $user)
    {
        $follower = auth()->user();
        if ($follower->id == $user->id) {
            return back()->withError("You can't follow yourself");
        }
        if(!$follower->isFollowing($user->id)) {
            $follower->follow($user->id);

            //Sending a notification
            $user->notify(new UserFollowed($follower));

            // New Pusher instance with config data
            $pusher = new \Pusher\Pusher(config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'), 
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options'));

            // Enable pusher logging, used an anonymous class and the Monolog
            $pusher->set_logger(new class {
                public function log($msg)
                  {
                      \Log::info($msg);
                  }
            });
            //  data to send to Pusher
            $data = [$follower->name.' is followed you'];
            $pusher->trigger( 'usr_'.$user->id, 'my-event', $data);
               

            return back()->withSuccess("You are now friends with {$user->name}");
        }
        return back()->withError("You are already following {$user->name}");
    }
    public function unfollow(User $user)
    {
        $follower = auth()->user();
        if($follower->isFollowing($user->id)) {
            $follower->unfollow($user->id);
            return back()->withSuccess("You are no longer friends with {$user->name}");
        }
        return back()->withError("You are not following {$user->name}");
    }
    public function notifications()
    {
        return auth()->user()->unreadNotifications()->limit(5)->get()->toArray();
    }

// --------------------- [ User Account ] ---------------------
    public function getAccount()
    {
        return view('users.account', ['user' => Auth::user()]);
    }

    public function postSaveAccount(Request $request)
    {
        $this->validate($request, [
           'name' => 'required|max:120'
        ]);

        $user = Auth::user();
        $old_name = $user->name;
        $user->name = $request['name'];
        $user->update();
        $file = $request->file('image');
        $filename = $request['name'] . '-' . $user->id . '.jpg';
        $old_filename = $old_name . '-' . $user->id . '.jpg';
        $update = false;
        //store in local storage so that only Authenticated member can access the images/files 
        //change name
        if (Storage::disk('local')->has($old_filename)) {
            $old_file = Storage::disk('local')->get($old_filename);
            Storage::disk('local')->put($filename, $old_file);
            $update = true;
        }
        //change photo
        if ($file) {
            Storage::disk('local')->put($filename, File::get($file));//to save the actual file
        }
        //change name and photo
        if ($update && $old_filename !== $filename) {
            Storage::delete($old_filename);
        }

        $file = Image::make($request->file('image')->getRealPath())->resize(100,100)->save($filename,90);      
        return redirect()->route('account');
    }
    public function getUserImage($filename)
    {   
        $file = Storage::disk('local')->get($filename);
        
        return $file;
        return response()->json($file, 200);
    }
}