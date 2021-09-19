<?php

namespace App\Http\Controllers;

use App\Jobs\GetUrls;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL as FacadesURL;

class UrlController extends Controller
{
    public function read()
    {
        return view('dashboard', ['urls' => Url::get()]);
    }

    public function create(Request $request)
    {
        if (preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$request->uri)) {
            
            $this->validate($request, [
                'uri' => 'required|unique:urls,uri'
            ]);

            Url::create([
                'uri' => $request->uri,
                'status' => "Processing...",
                'response' => "Processing...",
            ]);

            $message = 'The URL: '.$request->uri.' was added to the database successfully.';
            session(['message' => $message]);

            GetUrls::dispatch();
            return back();

        } else {
            return back()->withErrors('The URI provided is not on a valid format.');
        }
    }

    public function delete($id)
    {
        $url = Url::find($id);

        Storage::delete($url->response);
        $url->delete();

        $message = 'The URL was removed from the database successfully.';
        session(['message' => $message]);

        return back();
    }
    
    public function ajax()
    {
        return response()->json(array('ajax_response' => true));
    }
}
