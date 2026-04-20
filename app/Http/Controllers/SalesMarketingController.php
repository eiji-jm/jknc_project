<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesMarketingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        return view('sales-marketing.index');
    }

    public function earners()
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        return view('sales-marketing.earners.index');
    }

    public function ida()
    {
        if (!auth()->user()->hasPermission('access_sales_marketing')) {
            abort(403, 'Unauthorized');
        }

        return view('sales-marketing.ida.index');
    }
}