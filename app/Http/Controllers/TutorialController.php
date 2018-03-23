<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutorial;

class TutorialController extends Controller
{
    
	public function index()
	{
		//Menampilkan komentar juga
		// return Tutorial::with('comments')->get();

		return Tutorial::all();
	}

	public function show($id)
	{
		$tutorial = Tutorial::with('comments')->where('id', $id)->first();

		if(!$tutorial){
			return response()->json(['error' => 'Page Not Found'], 404);
		}

		return $tutorial;
	}

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required',
    		'body' => 'required'
    	]);

    	$tutorial = $request->user()->tutorials()->create([
    		'title' => $request->json('title'),
    		'slug' => str_slug($request->json('title')),
    		'body' => $request->json('body')
    	]);

    	return $tutorial;
    }

    public function update(Request $request, $id)
    {
    	$this->validate($request, [
    		'title' => 'required',
    		'body' => 'required'
    	]);

    	$tutorial = Tutorial::find($id);

    	//Cek kepemilikan tutotial
    	if($request->user()->id != $tutorial->user_id){
    		return response()->json(['error' => 'You have not permission to edit this tutorial', 403]);
    	}

    	$tutorial->title = $request->title;
    	$tutorial->body = $request->body;
    	$tutorial->save();

    	return $tutorial;
    }

    public function destroy(Request $request, $id)
    {
    	$tutorial = Tutorial::find($id);

    	//Cek kepemilikan tutotial
    	if($request->user()->id != $tutorial->user_id){
    		return response()->json(['error' => 'You have not permission to delete this tutorial', 403]);
    	}

    	$tutorial->delete();
    	return response()->json([
    		'success' => true, 
    		'message' => 'Delete success',
    		200]); 

    }
}
