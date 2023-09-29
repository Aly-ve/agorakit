<?php

namespace App\Http\Controllers;

use Auth;
use App\Group;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verified', 'auth']);
    }

    public function index(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');

            if (Auth::user()->getPreference('show', 'my') == 'admin') {
                // build a list of groups the user has access to
                if (Auth::user()->isAdmin()) { // super admin sees everything
                    $allowed_groups = Group::get()
                        ->pluck('id');
                }
            }

            if (Auth::user()->getPreference('show', 'my') == 'all') {
                $allowed_groups = Group::public()
                    ->get()
                    ->pluck('id')
                    ->merge(Auth::user()->groups()->pluck('groups.id'));
            }

            if (Auth::user()->getPreference('show', 'my') == 'my') {
                $allowed_groups = Auth::user()->groups()->pluck('groups.id');
            }


            $groups = Group::whereIn('id', $allowed_groups)
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'groups')->withQueryString();
            

          

            $users = \App\User::with('groups')
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'users');
            

            $discussions = \App\Discussion::whereIn('group_id', $allowed_groups)
            ->with('group')
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'discussions');
            
            

            $actions = \App\Action::whereIn('group_id', $allowed_groups)
            ->with('group')
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'actions');

            $files = \App\File::whereIn('group_id', $allowed_groups)
            ->with('group')
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'files');

            $comments = \App\Comment::with('discussion', 'discussion.group')
            ->whereHas('discussion', function ($q) use ($allowed_groups) {
                $q->whereIn('group_id', $allowed_groups);
            })
            ->search($query)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'comments');


            return view('search.results')
            ->with('groups', $groups)
            ->with('users', $users)
            ->with('discussions', $discussions)
            ->with('files', $files)
            ->with('comments', $comments)
            ->with('actions', $actions)
            ->with('query', $query);
        } else {
            return view('search.search');
        }
    }
}
