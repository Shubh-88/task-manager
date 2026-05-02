<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\File;

class FileController extends Controller
{
      public function index(Request $request)
    {
       $query = File::latest();

if ($request->search) {
    $query->where('file_path', 'like', '%' . $request->search . '%');
}

$files = $query->paginate(8)->withQueryString();

        return Inertia::render('Files/Index', [
            'files' => $files
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads', 'public');

           auth()->user()->files()->create([
    'file_path' => $path
]);
        }

        return redirect()->back()->with('success', 'Files uploaded successfully');
    }
}
