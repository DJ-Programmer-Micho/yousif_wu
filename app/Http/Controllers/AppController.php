<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;

class AppController extends Controller
{
    public function dashboard(){
        return view('pages.dashboard.index');
    }
    public function profile(){
        return view('pages.setting.profile');
    }
    public function setting(){
        return view('pages.setting.setting');
    }
    public function register(){
        return view('pages.auth.register');
    }
    public function index(){
        return view('layouts.app');
    }
    public function splash(){
        return view('splash.index');
    }

    public function splashSave(Request $request)
    {
        $validated = $request->validate([
            // Only western_union for now (others disabled). Add more when you enable them.
            'agency' => ['required', Rule::in(['western_union'])],
            // 3 languages for now
            'lang'   => ['required', Rule::in(['ar','en','ku'])],
        ]);

        // persist to session
        session([
            'agency' => $validated['agency'],
            'locale' => $validated['lang'],
        ]);

        // set app locale immediately for this request
        app()->setLocale($validated['lang']);
        App::setLocale($validated['lang']);

        return redirect()->route('dashboard');
    }

    public function sender(){
        return view('pages.sender.create');
    }
    public function reciever(){
        return view('pages.reciever.index');
    }
    public function countryLimit(){
        return view('pages.country_limit.index');
    }
    public function generalCountryLimit(){
        return view('pages.country_limit.g-index');
    }
    public function countryTax(){
        return view('pages.country_tax.index');
    }
    public function generalCountryTax(){
        return view('pages.country_tax.g-index');
    }
    public function countryRules(){
        return view('pages.country_rules.index');
    }
    public function countryInfo(){
        return view('pages.country_info.index');
    }
    public function senderPendingTransfer(){
        return view('pages.sender.pending');
    }
    public function senderExecutedTransfer(){
        return view('pages.sender.executed');
    }
    public function senderRejectedTransfer(){
        return view('pages.sender.rejected');
    }
    public function receiverPendingTransfer(){
        return view('pages.reciever.pending');
    }
    public function receiverExecutedTransfer(){
        return view('pages.reciever.executed');
    }
    public function receiverRejectedTransfer(){
        return view('pages.reciever.rejected');
    }
    public function bankStatement(){
        return view('pages.bank.index');
    }
    public function mtcn(){
        return view('pages.mtcn.index');
    }
    public function senderBalance(){
        return view('pages.balance.sender.index');
    }
    public function receiverBalance(){
        return view('pages.balance.receiver.index');
    }
    public function announcement(){
        return view('pages.announcement.index');
    }
}
