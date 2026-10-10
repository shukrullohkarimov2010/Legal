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
        return view('contract.codex', [
            'pythonApiUrl' => $this->pythonApi,
        ]);
    }
    public function compare()
    {
        return view('contract.compare');
    }
    public function kodex()
    {
        return view('contract.ugalov-kodex', [
            'pythonApiUrl' => $this->pythonApi,
        ]);
    }
    // Метод form() удалён: он возвращал view('contract.form'), а такого файла в
    // resources/views/contract/ нет. Маршрута на него тоже не было — /contract/form
    // ведёт на showForm(), — так что вызов сразу упал бы на «View not found».
}
