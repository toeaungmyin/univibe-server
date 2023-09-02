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

    public function deletePostReport(Request $request, $id)
    {
        try {
            $userReport = PostReport::find($id); // Retrieve the report by its ID

            if (!$userReport) {
                return response()->json(['message' => 'Report does not exist'], 404);
            }

            $userReport->delete();
            return response()->json(['message' => 'Report deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }


    public function getPostsReports()
    {
        $reports = PostReport::latest()->paginate(20);
        return response()->json(new PostReportCollection($reports));
    }

    public function deletePostReports(PostReport $postReport)
    {
        try {
            // Check if $postReport is null
            if (!$postReport) {
                return response()->json(['message' => 'Report does not exist'], 404);
            }

            // If $postReport is not null, it exists, so delete it
            $postReport->delete();

            return response()->json(['message' => 'Report deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
