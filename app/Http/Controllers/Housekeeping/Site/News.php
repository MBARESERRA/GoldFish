<?php
namespace App\Http\Controllers\Housekeeping\Site;

use Auth;
use Request;
use Illuminate\Http\Request as Req;
use App\Http\Controllers\Controller;
use App\Helpers\CMS;
use App\Models\CMS\News as Insert;
use Validate;

class News extends Controller
{
  public function Create(Req $request)
  {
    if(auth()->user()->rank >= CMS::fuseRights('news')){
      if (Request::isMethod('post'))
      {
        $validatedData = $request->validate([
          'title'   => 'required',
          'short' => 'required',
          'long' => 'required',
          'image' => 'required'
        ]);
        Insert::create([
          'caption' => request()->get('title'),
          'desc' => request()->get('short'),
          'body' => request()->get('long'),
          'image' => '/images/news/'.request()->get('image'),
          'author' => auth()->user()->id,
          'date' => time()
        ]);
        return redirect('housekeeping/site/news/list')->withErrors(['Created '.request()->get('title')]);
      }
      $images = \File::allFiles(public_path('images/news'));
      return view('site.createnews',
      [
        'group' => 'site',
        'images' => $images
      ]);
    }
    else {
      return redirect('dashboard');
    }
  }
  public function List()
  {
    if(auth()->user()->rank >= CMS::fuseRights('news')){
      $news = Insert::orderBy('date', 'DESC')->paginate(10);
      return view('site.newslist',
      [
        'group' => 'site',
        'news' => $news,
      ]);
    }
    else {
      return redirect('dashboard');
    }
  }
  public function Edit($id)
  {
    if(auth()->user()->rank >= CMS::fuseRights('news')){
      $news = Insert::where('id', $id)->first();
      if(!empty($news)) {
        if (Request::isMethod('post'))
        {
          Insert::where('id', $id)->update([
            'caption' => request()->get('title'),
            'desc' => request()->get('short'),
            'image' => '/images/news/'.request()->get('image'),
            'body' => request()->get('long'),
            'date' => time(),
          ]);
          return redirect('housekeeping/site/news/list')->withErrors(['Saved changes.']);
        }
        $images = \File::allFiles(public_path('images/news'));
        return view('site.editnews',
        [
          'group' => 'site',
          'news' => $news,
          'images' => $images
        ]);
      }
    }
    else {
      return redirect('dashboard');
    }
  }
  public function Delete($id)
  {
    if(auth()->user()->rank >= CMS::fuseRights('news')){
      Insert::where('id', $id)->delete();
      return redirect('housekeeping/site/news/list')->withErrors(['Deleted']);
    }
  }
}