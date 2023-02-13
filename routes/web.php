<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Events\GameStatusChange;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/setlocale/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'th'])) {
        abort(400);
    }

    \Session::put('locale', $locale);
    return redirect()->back();
    //
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'language'], function () {
    Route::get('/', function () {

        $user = Auth::user();
        if (!$user) {
            return view('welcome');
        }
        if ($user->user_level == 'admin') {// do your magic here
            return redirect()->route('admin-dashboard');
        } else if ($user->user_level == 'master-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'sub-agent') {
            # code...
            return redirect()->route('dashboard');

        } else if ($user->user_level == 'super-admin') {
            # code...
            return redirect()->route('superadmin');
        } else {
            return redirect()->route('player');
        }
    })->name('welcome');
    Route::get('/home', function () {

        $user = Auth::user();
        if (!$user) {
            return view('welcome');
        }
        if ($user->user_level == 'admin') {// do your magic here
            return redirect()->route('admin-dashboard');
        } else if ($user->user_level == 'master-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'sub-agent') {
            # code...
            return redirect()->route('dashboard');

        } else if ($user->user_level == 'super-admin') {
            # code...
            return redirect()->route('superadmin');
        } else {
            return redirect()->route('player');
        }
    })->name('home');

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/login');
    });

    Route::post('/register/submit', 'App\Http\Controllers\Auth\RegisterController@create')->name('register-submit');
    Route::get('/register/{referral_id}', 'App\Http\Controllers\Auth\RegisterController@index')->name('register');

    Route::get('/privacy', 'App\Http\Controllers\Auth\RegisterController@privacy')->name('privacy');
    Route::get('game-opened', 'App\Http\Controllers\HomeController@isOpen');

    Auth::routes();

// Route::get('/home', [App\Http\Controllers\Home	Controller::class, 'admin'])->name('home');
    Auth::routes();


    Route::group(['middleware' => 'auth'], function () {

        Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);

        // Route::get('upgrade', function () {return view('pages.upgrade');})->name('upgrade');
        //  Route::get('map', function () {return view('pages.maps');})->name('map');
        //  Route::get('table-list', function () {return view('pages.tables');})->name('table');

        // super admin
        Route::group(['middleware' => 'super-admin'], function () {
            Route::prefix('superadmin')->group(function () {
                Route::get('/', 'App\Http\Controllers\HomeController@superAdmin')->name('superadmin');
                Route::get('/income', 'App\Http\Controllers\DevAdminController@income')->name('superadmin-income');

                Route::prefix('wallet')->group(function () {
                    Route::get('/{user_id}', 'App\Http\Controllers\SuperAdminController@wallet')->name('superadmin-wallet');
                    Route::get('/{user_id}/logs', 'App\Http\Controllers\SuperAdminController@walletLogs')->name('superadmin-wallet-logs');
                    Route::post('/modify', 'App\Http\Controllers\SuperAdminController@wallet_modify')->name('superadmin-wallet-modify');
                });
                Route::prefix('commission')->group(function () {
                    Route::get('/{user_id}', 'App\Http\Controllers\DevAdminController@comission')->name('superadmin-comission');
                    Route::get('/{user_id}/logs', 'App\Http\Controllers\DevAdminController@comissionLog')->name('superadmin-comission-logs');
                    Route::post('/modify', 'App\Http\Controllers\DevAdminController@comission_modify')->name('superadmin-comission-modify');
                });

                Route::get('/active-players', 'App\Http\Controllers\SuperAdminController@activePlayers')->name('superadmin-active-players');
                Route::get('/clear-players', 'App\Http\Controllers\DevAdminController@')->name('superadmin-clear-players');
                Route::post('/password/reset', 'App\Http\Controllers\DevAdminController@reset_password')->name('superadmin-reset-password');
                Route::post('/active-players/change-status', 'App\Http\Controllers\DevAdminController@changePlayerStatus')->name('superadmin-players-change-status');
                Route::post('/active-players/change-level', 'App\Http\Controllers\DevAdminController@changeLevel')->name('superadmin-players-change-level');
                Route::get('/player-deactivated', 'App\Http\Controllers\DevAdminController@clearPlayers')->name('superadmin-deactivated-players');
                Route::get('/clear-player-deactivated', 'App\Http\Controllers\DevAdminController@cleardeactivatedPlayers')->name('superadmin-clear-deactivated-players');
                //Route::get('/clear-agent-list', 'App\Http\Controllers\DevAdminController@clearagentList')->name('superadmin-clear-agent-list');
                Route::get('/superadmin-sub-agent-list', 'App\Http\Controllers\DevAdminController@subAgentList')->name('superadmin-sub-agent-list');
                //Route::get('/superadmin-clear-sub-agent-list', 'App\Http\Controllers\DevAdminController@clearsubAgentList')->name('superadmin-clear-sub-agent-list');
                Route::get('/site-settings', 'App\Http\Controllers\SuperAdminController@siteSettings')->name('superadmin-site-settings');
                Route::post('/site-settings-save', 'App\Http\Controllers\SuperAdminController@siteSettingsSave')->name('superadmin-site-settings-save');
                Route::view('/round-viewer', 'superAdmin.pages.currentGame')->name('superadmin-round-viewer');
                Route::post('/edit-user-info', 'App\Http\Controllers\DevAdminController@editUserInfo')->name('edit-user-info');

                Route::name('sadmin.')->group(function () {
                    Route::name('player.')->group(function () {
                        Route::prefix('player')->group(function () {
                            Route::get('/history/{user_id}', 'App\Http\Controllers\SuperAdminController@playerHistory')->name('history');
                        });
                    });
                    Route::name('agent.')->group(function () {
                        Route::prefix('agent')->group(function () {
                            Route::get('/operator', 'App\Http\Controllers\SuperAdminController@agentList')->name('operator');
                            Route::get('/sub/operator', 'App\Http\Controllers\SuperAdminController@subAgentList')->name('sub.operator');
                            Route::get('/master', 'App\Http\Controllers\SuperAdminController@goldAgentList')->name('master');
                            Route::get('/gold', 'App\Http\Controllers\SuperAdminController@silverAgentList')->name('gold');
                            Route::get('/silver', 'App\Http\Controllers\SuperAdminController@bronzeAgentList')->name('silver');
//                            Route::get('/bronze', 'App\Http\Controllers\SuperAdminController@bronzeAgentList')->name('bronze');
                            Route::get('/history/{user_id}', 'App\Http\Controllers\SuperAdminController@agentHistory')->name('history');
                        });
                    });
                    Route::name('archive.')->group(function () {
                        Route::prefix('archive')->group(function () {
                            Route::get('/game', 'App\Http\Controllers\SuperAdminController@arciveGame')->name('game');
                            Route::get('/betting', 'App\Http\Controllers\SuperAdminController@arciveBetting')->name('betting');
                            Route::get('/winner', 'App\Http\Controllers\SuperAdminController@arciveWinner')->name('winner');
                        });
                        Route::name('ajax.')->group(function () {
                            Route::get('/game', 'App\Http\Controllers\SuperAdminController@games')->name('game');
                            Route::get('/betting', 'App\Http\Controllers\SuperAdminController@bettings')->name('betting');
                        });
                    });
                    Route::group(['prefix' => 'route'], function () {
                        Route::get('/', ['as' => 'list', 'uses' => 'App\Http\Controllers\HomeController@routes']);
                    });
                });
            });
        });
        //  admin
        Route::group(['middleware' => 'admin'], function () {
            Route::prefix('admin')->group(function () {
                Route::get('/', 'App\Http\Controllers\HomeController@agent')->name('admin-dashboard');
                Route::get('/logs', 'App\Http\Controllers\HomeController@logs')->name('admin_Logs');
                Route::get('/summary', 'App\Http\Controllers\AdminController@summary')->name('admin-summary');

                Route::prefix('wallet')->group(function () {
                    Route::get('/', 'App\Http\Controllers\AdminController@wallet')->name('admin-agent-wallet');
                    Route::post('/', 'App\Http\Controllers\AdminController@wallet_modify')->name('admin-agent-wallet-modify');
                    Route::get('/logs', 'App\Http\Controllers\AdminController@walletLogs')->name('admin-agent-wallet-logs');
                });

                Route::prefix('commission')->group(function () {
                    Route::get('/', 'App\Http\Controllers\AdminController@comission')->name('admin-agent-comission');
                    Route::post('/', 'App\Http\Controllers\AdminController@comission_modify')->name('admin-agent-comission-modify');
                    Route::get('/logs', 'App\Http\Controllers\AdminController@comissionLogs')->name('admin-comission-logs');
                    Route::get('/log', 'App\Http\Controllers\AdminController@comissionLog')->name('admin-agent-comission-logs');
                    Route::get('/archive', 'App\Http\Controllers\AdminController@comissionArchive')->name('admin-agent-comission-archive');
                    Route::get('/edit', 'App\Http\Controllers\AdminController@editCommission')->name('admin-edit-comission');
                    Route::get('/update/{commission?}/{user_id?}/', 'App\Http\Controllers\AdminController@updateCommission')->name('admin-update-comission');
                });

                Route::prefix('agent')->group(function () {
                    Route::get('/sub/operator', 'App\Http\Controllers\AdminController@agentList')->name('sub-operator-list');
                    Route::get('/master', 'App\Http\Controllers\AdminController@subAgentList')->name('master-agent-list');
                    Route::get('/gold', 'App\Http\Controllers\AdminController@goldAgentList')->name('gold-agent-list');
                    Route::get('/silver', 'App\Http\Controllers\AdminController@silverAgentList')->name('silver-agent-list');
                    Route::get('/bronze', 'App\Http\Controllers\AdminController@bronzeAgentList')->name('bronze-agent-list');
                });

                Route::get('/active-players', 'App\Http\Controllers\AdminController@activePlayers')->name('admin-active-players');
                //Route::get('/clear-active-players', 'App\Http\Controllers\AdminController@clearactivePlayers')->name('admin-clear-active-players');
                Route::post('/active-players/change-status', 'App\Http\Controllers\AdminController@changePlayerStatus')->name('admin-players-change-status');
                Route::post('/active-players/change-level', 'App\Http\Controllers\AdminController@changeLevel')->name('admin-players-change-level');
                Route::get('/user-approval', 'App\Http\Controllers\AdminController@userApproval')->name('admin-user-approval');
                Route::post('/user-approval/confirm', 'App\Http\Controllers\AdminController@userApproveConfirm')->name('admin-user-approval-confirm');
                Route::get('/player-deactivated', 'App\Http\Controllers\AdminController@deactivatedPlayers')->name('admin-deactivated-players');
//                Route::get('/clear-player-deactivated', 'App\Http\Controllers\AdminController@cleardeactivatedPlayers')->name('admin-clear-deactivated-players');

                //Route::get('/clear-agent-list', 'App\Http\Controllers\AdminController@clearagentList')->name('admin-clear-agent-list');

                //Route::get('/clear-sub-agent-list', 'App\Http\Controllers\AdminController@clearsubAgentList')->name('admin-clear-sub-agent-list');
                Route::get('/site-settings', 'App\Http\Controllers\AdminController@siteSettings')->name('site-settings');
                Route::post('/site-settings-save', 'App\Http\Controllers\AdminController@siteSettingsSave')->name('site-settings-save');
                Route::post('/modify-game', 'App\Http\Controllers\AdminController@modifyGame')->name('modify-game');
                Route::post('/add-game', 'App\Http\Controllers\AdminController@addGame')->name('add-game');
                Route::view('/round-viewer', 'agent.pages.currentGame')->name('round-viewer');
                Route::post('/edit-user-info', 'App\Http\Controllers\AdminController@editUserInfo')->name('edit-user-info');

                Route::name('admin.')->group(function () {
                    Route::name('player.')->group(function () {
                        Route::prefix('player')->group(function () {
                            Route::get('/history/{user_id}', 'App\Http\Controllers\AdminController@showHistory')->name('history');
                            Route::get('/betting', 'App\Http\Controllers\AdminController@liveBetting')->name('betting');
                        });
                    });
                    Route::name('agent.')->group(function () {
                        Route::prefix('agent')->group(function () {
                            Route::get('/history/{user_id}', 'App\Http\Controllers\AdminController@agentHistory')->name('history');
                            Route::post('/password/reset/', 'App\Http\Controllers\AdminController@resetPassword')->name('password.reset');
                        });
                    });
                    Route::group(['prefix' => 'route'], function () {
                        Route::get('/', ['as' => 'list', 'uses' => 'App\Http\Controllers\HomeController@routes']);
                    });
                    Route::name('game.')->group(function () {
                        Route::prefix('game')->group(function () {
                            Route::get('/reset', ['as' => 'reset', 'uses' => 'App\Http\Controllers\AdminController@resetGame']);
                        });
                    });
                });
            });
            Route::post('/upload-betting-video', 'App\Http\Controllers\AdminController@uploadVideo')->name('upload-betting-video');
        });
        // Agent
        Route::group(['middleware' => 'agent'], function () {
            Route::prefix('agent')->group(function () {
                Route::get('/', 'App\Http\Controllers\HomeController@agent')->name('dashboard');
                Route::get('/summary', 'App\Http\Controllers\AgentController@summary')->name('summary');
                Route::prefix('wallet')->group(function () {
                    Route::get('/', 'App\Http\Controllers\AgentController@wallet')->name('agent-wallet');
                    Route::post('/', 'App\Http\Controllers\AgentController@wallet_modify')->name('agent-wallet-modify');
                    Route::get('/logs', 'App\Http\Controllers\AgentController@walletLogs')->name('agent-wallet-logs');
                });
                Route::prefix('commission')->group(function () {
                    Route::get('/', 'App\Http\Controllers\AgentController@comission')->name('agent-comission');
                    Route::post('/', 'App\Http\Controllers\AgentController@comission_modify')->name('agent-comission-modify');
                    Route::get('/log', 'App\Http\Controllers\AgentController@comissionLog')->name('agent-comission-logs');
                    Route::get('/logs', 'App\Http\Controllers\AgentController@comissionLogs')->name('comission-logs');
                    Route::get('/archive', 'App\Http\Controllers\AgentController@comissionArchive')->name('agent-comission-archive');
                    Route::get('/edit', 'App\Http\Controllers\AgentController@comissionEdit')->name('agent-comission-edit');
                    Route::get('/update/{commission?}/{user_id?}/', 'App\Http\Controllers\AgentController@updateCommission')->name('agent-update-comission');
                });
                Route::name('transaction.')->group(function () {
                    Route::prefix('transaction')->group(function () {
                        Route::get('/log', 'App\Http\Controllers\AgentController@showTransactions')->name('log');
                    });
                });
                Route::name('player.')->group(function () {
                    Route::prefix('player')->group(function () {
                        Route::get('/history/{player_id}', 'App\Http\Controllers\AgentController@showHistory')->name('history');
                    });
                });
                Route::get('/active-players', 'App\Http\Controllers\AgentController@activePlayers')->name('active-players');
                Route::post('/active-players/change-level', 'App\Http\Controllers\AgentController@changeLevel')->name('players-change-level');
                Route::get('/agent-list', 'App\Http\Controllers\AgentController@agentList')->name('agent-list');
            });

            Route::post('/agent/active-players/change-status', 'App\Http\Controllers\AgentController@changePlayerStatus')->name('players-change-status');
            Route::get('/agent/user-approval', 'App\Http\Controllers\AgentController@userApproval')->name('user-approval');
            Route::post('/agent/user-approval/confirm', 'App\Http\Controllers\AgentController@userApproveConfirm')->name('user-approval-confirm');
            Route::get('/agent/player-deactivated', 'App\Http\Controllers\AgentController@deactivatedPlayers')->name('deactivated-players');
            Route::get('/agent/withdraw-request', 'App\Http\Controllers\AgentController@withdrawalRequest')->name('withdraw-request');
            Route::post('/agent/withdraw-request/confirm', 'App\Http\Controllers\AgentController@withdrawalRequestConfirm')->name('withdraw-request-confirm');
            Route::get('/agent/withdraw-request/history', 'App\Http\Controllers\AgentController@withdrawalRequestHistory')->name('withdraw-request-history');

            Route::get('/agent/transaction-log', 'App\Http\Controllers\TransactionController@showTransactionLog')->name('transaction-log');
            Route::get('/logs', 'App\Http\Controllers\HomeController@logs')->name('Logs');

        });
        // player
        Route::group(['middleware' => 'player'], function () {
            Route::prefix('player')->group(function () {
                Route::get('/', 'App\Http\Controllers\HomeController@player')->name('player');
                //  player long polling
                Route::get('/game-details', 'App\Http\Controllers\HomeController@game_details_bet')->name('game-details-bet');
                Route::post('/bet', 'App\Http\Controllers\PlayerController@bet')->name('player-bet');
                Route::get('/withdraw', 'App\Http\Controllers\PlayerController@withdraw')->name('player-withdraw');
                Route::post('/withdraw', 'App\Http\Controllers\PlayerController@postWithdraw')->name('player-withdraw');
                Route::get('/betting-history', 'App\Http\Controllers\PlayerController@bettingHistory')->name('betting-history');
                Route::get('/bet_details/{game_id}', 'App\Http\Controllers\PlayerController@bettingDetails')->name('betting-details');
                Route::get('/settings', ['as' => 'player.settings', 'uses' => 'App\Http\Controllers\ProfileController@settings']);
                Route::get('/declare/winner', ['as' => 'player.winner', 'uses' => 'App\Http\Controllers\PlayerController@declareWinner']);

            });
            //  Route::post('/player/deposit', 'App\Http\Controllers\PlayerController@deposit');
            //  Route::get('/player/wallet', 'App\Http\Controllers\PlayerController@wallet');
            Route::prefix('profile')->group(function () {
                Route::put('/player/password', ['as' => 'player.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
                Route::get('/player', ['as' => 'player.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
                Route::put('/player/settings', ['as' => 'player.settingsUpdate', 'uses' => 'App\Http\Controllers\ProfileController@settingsUpdatePlayer']);

            });
        });

        Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
        Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
        Route::put('profile/settings', ['as' => 'profile.settingsUpdate', 'uses' => 'App\Http\Controllers\ProfileController@settingsUpdate']);
        Route::get('settings', ['as' => 'profile.settings', 'uses' => 'App\Http\Controllers\ProfileController@settings']);

    });
});

Route::get('test', 'App\Http\Controllers\HomeController@test');
