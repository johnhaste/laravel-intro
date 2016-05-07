<?php 

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use App\NiceAction; //To use the Model
use App\NiceActionLog; //To use the Model
use DB;

class NiceActionController extends Controller 
{
    
     //The route that will use this view is set on routes.php
    public function getHome()
    {
        $actions = NiceAction::All(); //Once we used "use App\Niceaction" model, we can retrieve data from it
        $actions = DB::table('nice_actions')->get();
        $logged_actions = NiceActionLog::All(); //Same as SELECT * FROM TABLE NICEACTIONLOG
        return view('home', ['actions' => $actions, 'logged_actions' => $logged_actions]);
    }
    
    public function getNiceAction($action, $name = null)
    {
        
        if($name === null)//Defining a default value 
        {
            $name = 'you';
        }
        
        $nice_action = NiceAction::where('name', $action)->first(); //Finds the specific action
        $nice_action_log = new NiceActionLog(); //Starts the log Model
        $nice_action->logged_actions()->save($nice_action_log); //starts the function logged_actions and saves the $nice_action_log
        
        //Returns the View actions/nice.blade.php with 2 parameters
        return view('actions.'. $action, ['action' => $action, 'name' => $name]);
    }
    
    private function transformnName($name){
        $prefix = 'KING ';
        return $prefix . strtoupper($name);
    }
    
    public function postInsertNiceAction(Request $request)
    {
        //Validates the data the user has inputed
        $this->validate($request,[
           'name' => 'required|alpha|unique:nice_actions',
           'niceness' => 'required|numeric',
        ]);
        
        //Prepares a new action to be addedd
        $action = new NiceAction(); //Based on the NiceAction Model in app/NiceAction.php
        $action->name = ucfirst(strtolower($request['name']));
        $action->niceness = $request['niceness'];
        $action->save();
        
        //Selects all data from database for the nav
        $actions = NiceAction::all();
        
        return redirect()->route('home', ['actions' => $actions]);
    }
    
}

?>