<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\AgentDetails;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        if (in_array(auth()->user()->user_level, ['master-agent-player', 'sub-agent-player'])) {

            return view('player.profile.edit');
        } else if (auth()->user()->user_level == 'super-admin') {
            return view('superAdmin.profile.edit');
        } else {

            return view('agent.profile.edit');
        }

    }

    public function settings()
    {
        if (in_array(auth()->user()->user_level, ['master-agent-player', 'sub-agent-player'])) {

            return view('player.profile.settings');
        } else if (auth()->user()->user_level == 'super-admin') {
            return view('superAdmin.profile.settings');
        } else {
            return view('agent.profile.settings');

        }

    }

    /**
     * Update the profile
     *
     * @param \App\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        if (auth()->user()->id == 1) {
            return back()->withErrors(['not_allow_profile' => __('You are not allowed to change data for a default user.')]);
        }

        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param \App\Http\Requests\PasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        if (auth()->user()->id == 1) {
            return back()->withErrors(['not_allow_password' => __('You are not allowed to change the password for a default user.')]);
        }

        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }

    public function settingsUpdate(Request $request)
    {
        $user = auth()->user()->agent_details;
        if ($user) {
            $user->user_id = auth()->user()->id;
            $user->contact_person = $request->contact_person;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->details = $request->details;
            $user->player_phone_number = $request->player_phone_number;
            $user->facebook_link = $request->facebook_link;
            $user->save();
        } else {
            $user = new AgentDetails();
            $user->user_id = auth()->user()->id;
            $user->contact_person = $request->contact_person;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->details = $request->details;
            $user->player_phone_number = $request->player_phone_number;
            $user->facebook_link = $request->facebook_link;
            $user->save();
        }
        return redirect()->back()->with('success', 'details updated');

    }

    public function settingsUpdatePlayer(Request $request)
    {
        Validator::make($request->all(), [
            'phone_number' => ['required', 'numeric', 'unique:users,phone_number,' . auth()->user()->id],
            'facebook_link' => ['required', 'string'],
        ])->validate();
        $user = auth()->user();
        $user->phone_number = $request->phone_number;
        $user->facebook_link = $request->facebook_link;
        $user->save();
        return redirect()->back()->with('success', 'details updated');

    }
}
