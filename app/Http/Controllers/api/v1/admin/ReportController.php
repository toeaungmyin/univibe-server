<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\admin\PostReportCollection;
use App\Http\Resources\admin\ReportCollection;
use App\Http\Resources\admin\ReportResource;
use App\Models\PostReport;
use App\Models\UserReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getUsersReports()
    {
        $reports = UserReport::latest()->paginate(20);
        return response()->json(new ReportCollection($reports));
    }

    public function getPostsReports()
    {
        $reports = PostReport::latest()->paginate(20);
        return response()->json(new PostReportCollection($reports));
    }

    public function deleteUserReport(UserReport $report)
    {
        try {

            if (!$report) {
                return response()->json(['message' => 'Report does not exist'], 404);
            }

            $report->delete();

            return response()->json(['message' => 'Report dd deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500); // Use a more appropriate status code for server errors (e.g., 500)
        }
    }

    public function deletePostReport(PostReport $report)
    {
        try {

            if (!$report) {
                return response()->json(['message' => 'Report does not exist'], 404);
            }

            $report->delete();

            return response()->json([
                'message' => 'Report deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500); // Use a more appropriate status code for server errors (e.g., 500)
        }
    }



    public function searchUserReport(Request $request)
    {
        $query = $request->input('query');

        $user_reports = UserReport::join('users', 'user_reports.id', '=', 'users.id')
            ->where('user_reports.title', 'like', '%' . $query . '%')
            ->orWhere('user_reports.description', 'like', '%' . $query . '%')
            ->orWhere('users.username', 'like', '%' . $query . '%')
            ->paginate(20);

        return response()->json(new ReportCollection($user_reports));
    }

    public function searchPostReport(Request $request)
    {
        $query = $request->input('query');

        $post_reports = PostReport::join('users', 'post_reports.id', '=', 'users.id')
            ->where('post_reports.title', 'like', '%' . $query . '%')
            ->orWhere('post_reports.description', 'like', '%' . $query . '%')
            ->orWhere('users.username', 'like', '%' . $query . '%')
            ->paginate(20);

        return response()->json(new PostReportCollection($post_reports));
    }



}
