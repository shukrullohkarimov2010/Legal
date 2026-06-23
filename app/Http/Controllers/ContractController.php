<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected string $pythonApi;

    public function __construct()
    {
        $this->pythonApi = env('PYTHON_API_URL', 'http://127.0.0.1:5000');
    }

    public function showForm()
    {
        return view('contract.analyze', [
            'pythonApiUrl' => $this->pythonApi,
        ]);
    }

    public function index()
    {
        return view('contract.analyze');
    }

    public function create()
    {
        return view('contract.create');
    }
    public function generate()
    {
        return view('contract.generate');
    }

    public function codex()
    {
        return view('contract.codex');
    }
    public function compare()
    {
        return view('contract.compare');
    }

}
