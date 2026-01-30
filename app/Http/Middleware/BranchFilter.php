<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class BranchFilter
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Assign a current branch if missing
            if (!$user->current_branch_id) {
                $this->assignCurrentBranch($user);
            }

            // Eager load the current branch relationship
            if (!$user->relationLoaded('currentBranch')) {
                $user->load('currentBranch');
            }

            // Share data with views
            $this->shareWithViews($user);
        }

        return $next($request);
    }

    /**
     * Assign a current branch to the user atomically.
     */
    private function assignCurrentBranch(\App\Models\User $user): void
    {
        $branchId = $user->defaultBranch()?->id;

        if (!$branchId) {
            return; // User has no accessible branches
        }

        // Use atomic update to prevent race conditions
        \DB::table('users')
            ->where('id', $user->id)
            ->whereNull('current_branch_id')
            ->update(['current_branch_id' => $branchId]);

        // Update in-memory instance
        $user->setAttribute('current_branch_id', $branchId);
    }

    /**
     * Share branch data with views.
     */
    private function shareWithViews(\App\Models\User $user): void
    {
        view()->share('currentBranch', $user->currentBranch);
        view()->share('userBranches', $user->accessibleBranches()->get());
    }
}
