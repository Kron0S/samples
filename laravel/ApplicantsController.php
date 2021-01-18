<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;

class ApplicantsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage');
        $page = $request->input('page');
        $sorter = json_decode($request->input('sorter'));
        $filter = $request->input('filter');
        $query = Applicant::orderBy($sorter->column, $sorter->asc ? "asc": "desc")
            ->withCount('violation_protocols')
            ->skip($page * $itemsPerPage)->take($itemsPerPage);
        if ($filter) {
            $query->where(function($q) use ($filter) {
                $q->where('name', 'ilike', '%' . $filter . '%')
                    ->orWhere('applicant_id', 'ilike', '%' . $filter . '%')
                    ;
            });
        }
        $items = $query->get();
        $pages = Applicant::count();
        if ($filter) {
            $pages = Applicant::where(function($q) use ($filter) {
                $q->where('name', 'ilike', '%' . $filter . '%')
                    ->orWhere('applicant_id', 'ilike', '%' . $filter . '%')
                    ;
            })->count();
        }
        return response()->json([
            'items' => $items,
            'pages' => ceil($pages / $itemsPerPage),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'             => 'required|min:1|max:50',
            'is_organisation'         => 'required',
        ]);
        $item = new Applicant();
        $item->name     = $request->input('name');
        $item->is_organisation = $request->input('is_organisation');
        $item->save();
        return response()->json( ['status' => 'success'] );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Applicant::find($id);
        return response()->json( [ 'item' => $item ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'             => 'required|min:1|max:50',
            'is_organisation'         => 'required',
        ]);
        $item = Applicant::find($id);
        $item->name     = $request->input('name');
        $item->is_organisation = $request->input('is_organisation');
        $item->save();
        return response()->json( ['status' => 'success'] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Applicant::find($id);
        if($item){
            $item->delete();
        }
        return response()->json( ['status' => 'success'] );
    }
}
