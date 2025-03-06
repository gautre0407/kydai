<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Thêm DB để dùng transaction

class ProblemController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('mantis.problem.index');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'topic' => 'required',
            'level' => 'required',
            'album' => 'nullable|string|max:255',
            'decentralization' => 'required|in:publish,limit,hidden',
            'first_move' => 'required|in:black,white',
            'scale' => 'required|in:part,full',
            'question' => 'required|string',
        ]);

        // DB::beginTransaction();

        // try {
        $album_id = $request->input('album_id');

        if (!$album_id && $request->filled('album')) {
            $album = Album::create([
                'name' => $request->input('album'),
                "role" => "owner",
                "parent_id" => null,
                'user_id' => 1
            ]);
            $album_id = $album->id;
        }

        $problem = Problem::create([
            'title' => $validated['title'],
            'topic_id' => $validated['topic'],
            'level_id' => $validated['level'],
            'decentralization' => $validated['decentralization'],
            'first_move' => $validated['first_move'],
            'scale' => $validated['scale'],
            'question' => $validated['question'],
            'album_id' => $album_id ?? null,
            'status' => Problem::STATUS_UNVERIFIED,
            'user_id' => 1
        ]);

        // DB::commit();

        return redirect()->route('problem.result_add', $problem->id);
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        // }
    }

    public function result_add($id)
    {
        $problem = Problem::find($id);
        if (!$problem) return redirect()->route('home');


        return view('mantis.problem.result_add', compact('problem'));
    }

    public function result_save($id, Request $request)
    {
        $problem = Problem::find($id);
        if (!$problem) return redirect()->route('home');
        $request->validate([
            'result' => 'required|string',
        ]);

        $updateData = [
            'result' => $request->result,
        ];
        $problem->update($updateData);

        return redirect()->route('problem.play', $problem->id);
    }

    public function play($id)
    {
        $problem = Problem::find($id);
        if (!$problem) return redirect()->route('home');


        return view('mantis.problem.play', compact('problem'));
    }
}
