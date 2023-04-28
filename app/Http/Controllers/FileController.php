<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Post;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $post = new Post();
        if($request->hasFile('file')){
            $completeFileName = $request->file('file')->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $compFile = str_replace(' ','_', $fileNameOnly).'_'.date('Y-m-d'). '.'.$extension;
            $path = $request->file('file')->storeAs('public/JustificationForms', $compFile);
            $post->file = $compFile;
        }
        if($post->save()){
            return response()->json(['status' => true, 'message' => 'File Saved Successfully']);
        }else{
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    public function return(Request $request, $id)
    {
        $file = Post::findOrFail($id);
        $file->status = 'Returned';
        $file->comments = $request->input('comments');
        $file->save();

        return response()->json(['status' => 'success']);
    }

    public function downloadFile($file) {
        return response()->download(public_path('storage/JustificationForms/' . $file), $file);
    }

    public function viewFile($file) {
        $path = public_path('storage/JustificationForms/' . $file);
        $fileData = file_get_contents($path);
        $response = response($fileData, 200)->header('Content-Type', mime_content_type($path));
        return $response;
    }

    public function show()
    {
        $posts = Post::all();
        foreach ($posts as $post) {
            $post->url = url('api/documents/' . $post->file);
        }
        return response()->json(['status' => true, 'file' => $posts]);
    }


    public function approve($file)
    {
        $file = Post::findOrFail($file);
        $file->status = 'Approved';
        $file->save();
        return response()->json(['status' => 'success']);
    }

    public function reject($file)
    {
        $file = Post::findOrFail($file);
        $file->status = 'Rejected';
        $file->save();
        return response()->json(['status' => 'success']);
    }




}
