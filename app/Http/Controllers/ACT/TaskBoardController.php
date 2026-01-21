<?php

namespace App\Http\Controllers\ACT;

use App\Http\Controllers\Controller;
use App\Models\TaskBoard;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskBoard::where('module', 'act')
            ->with(['taskable.customer', 'taskable.office']);

        if ($request->filled('status')) {
            $query->where('task_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('taskable', function($q) use ($request) {
                $q->where('do_number', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->latest()->paginate(15);

        $stats = [
            'pending' => TaskBoard::where('module', 'act')->where('task_status', 'pending')->count(),
            'in_progress' => TaskBoard::where('module', 'act')->where('task_status', 'in_progress')->count(),
            'completed' => TaskBoard::where('module', 'act')->where('task_status', 'completed')->count(),
        ];

        return view('pages.act.task_board.index', compact('tasks', 'stats'));
    }
}
