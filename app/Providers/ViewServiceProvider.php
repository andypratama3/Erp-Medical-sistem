<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            // ===============================
            // Assign current branch if missing
            // ===============================
            if (!$user->current_branch_id) {
                $this->assignCurrentBranch($user);
            }

            // ===============================
            // Load relation safely
            // ===============================
            if (!$user->relationLoaded('currentBranch')) {
                $user->load('currentBranch');
            }

            // ===============================
            // Share to all views
            // ===============================
            $this->shareWithViews($user, $view);
        });
    }

    /**
     * Assign current branch atomically
     */
    private function assignCurrentBranch(\App\Models\User $user): void
    {
        $branchId = $user->defaultBranch()?->id;

        if (!$branchId) {
            return;
        }

        \DB::table('users')
            ->where('id', $user->id)
            ->whereNull('current_branch_id')
            ->update(['current_branch_id' => $branchId]);

        // sync memory
        $user->setAttribute('current_branch_id', $branchId);
    }

    /**
     * Share branch data with views
     */
    private function shareWithViews(\App\Models\User $user, $view): void
    {
        $view->with('currentBranch', $user->currentBranch);
        $view->with(
            'userBranches',
            $user->accessibleBranches()->get()
        );
    }
}
