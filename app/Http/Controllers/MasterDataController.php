<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Display master data management page
     */
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        
        return view('master-data.index', compact('departments', 'positions'));
    }

    /**
     * Store new department
     */
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments,name',
            'code' => 'nullable|unique:departments,code',
            'description' => 'nullable|string'
        ]);

        Department::create($request->all());

        return redirect()->back()->with('success', 'Department berhasil ditambahkan!');
    }

    /**
     * Update department
     */
    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $department->id,
            'code' => 'nullable|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $department->update($request->all());

        return redirect()->back()->with('success', 'Department berhasil diupdate!');
    }

    /**
     * Delete department
     */
    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department berhasil dihapus!');
    }

    /**
     * Store new position
     */
    public function storePosition(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:positions,name',
            'code' => 'nullable|unique:positions,code',
            'description' => 'nullable|string'
        ]);

        Position::create($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan!');
    }

    /**
     * Update position
     */
    public function updatePosition(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|unique:positions,name,' . $position->id,
            'code' => 'nullable|unique:positions,code,' . $position->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $position->update($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil diupdate!');
    }

    /**
     * Delete position
     */
    public function destroyPosition(Position $position)
    {
        $position->delete();
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus!');
    }
}
