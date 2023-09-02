<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Models\UserReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::role('user')->count();
        $postCount = Post::count();
        $userReportCount = UserReport::count();
        $postReportCount = PostReport::count();

        $data = [
            'user_count' => $userCount,
            'post_count' => $postCount,
            'user_report_count' => $userReportCount,
            'post_report_count' => $postReportCount,
        ];

        return response()->json($data);
    }

    public function getUsersByDate(Request $request)
    {
        // $this->validate($request, [
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date|after_or_equal:start_date',
        // ]);

        // $start_date = $request->input('start_date');
        // $end_date = $request->input('end_date');


        $users = User::role('user')->get();
        // $users = User::whereBetween('created_at', [$start_date, $end_date])->get();

        $userData = $this->formatUserData($users);

        return response()->json(['userData' => $userData]);
    }

    private function formatUserData($users)
    {
        // Example: Count the number of users per day
        $userData = [];

        foreach ($users as $user) {
            $date = Carbon::parse($user->created_at)->format('Y-m-d');
            if (!isset($userData[$date])) {
                $userData[$date] = 1;
            } else {
                $userData[$date]++;
            }
        }

        return $userData;
    }
}
