@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Performance</h1>
            </div>

            <button type="button" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-60 p-3 text-left">Section</th>
                            <th class="p-3 text-left">Description</th>
                            <th class="w-40 p-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="border-t"><td class="p-3">Employee Evaluation Form</td><td class="p-3">Evaluation records for employees</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">PIP Program</td><td class="p-3">Performance improvement program records</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Awards</td><td class="p-3">Award list and award management</td><td class="p-3 text-gray-400">No data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection