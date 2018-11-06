<?php

use Illuminate\Http\Request;


/**********************ERRORS********************************************************
 * Types of errors that can be returned from the API
 * syntax : ['error'=>'error_type']
 * 
 * ERROR TYPE               |       MEANING
 * _____________________________________________________________________________
 * TOKEN_INVALID            ->  JWT token is invalid
 * TOKEN_EXPIRED            ->  JWT token is used after its expires_in time
 * TOKEN_BLACKLISTED        ->  JWT token is blacklisted and have to fetch 
 *                              a new one by logging in again
 * 
 * UNAUTHORIZED_REQUEST     ->  The request is unauthorized (No token used)
 * 
 * MODEL_NOT_FOUND          ->  Model not found for the id (need to change this to normal URL_NOT_FOUND before deploying for security reasons)
 * URL_NOT_FOUND            ->  URL is invalid (It is not handled in api.php)
 * ACCESS_DENIED            ->  User is denied access to the resource 
 *                              (User not in the group/house etc)
 ************************************************************************************/


/**********************CREATION********************************************************
 * Types of errors that can be returned from the API
 * syntax : ['status'=>'status_type']
 * STATUS TYPE               |       MEANING
 * _____________________________________________________________________________
 * 
 * CREATED                  ->  The resource is created
 * FAILED                   ->  The resource creation is failed
 ************************************************************************************/


//---------------------------------------------------------------
// Authentication Routes

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login')->name('auth.login');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::post('refresh', 'AuthController@refresh')->name('auth.refresh');
    Route::post('register', 'AuthController@register')->name('auth.register');
    Route::post('me', 'AuthController@me')->name('auth.me');
});

//---------------------------------------------------------------


Route::group(['namespace' => 'API', 'middleware' => ['api', 'jwt.auth']], function ($router) {

//---------------------------------------------------------------
// User Routes

    /**
     * Normal CRUD Routes
     */
    Route::get('/users', 'UserController@index')->name('users.index');
    Route::post('/users', 'UserController@store')->name('users.store');
    Route::delete('/users/{user}', 'UserController@destroy')->name('users.destroy');
    Route::match(array('PUT', 'PATCH'), "/users/{user}", array(
        'uses' => 'UserController@update',
        'as' => 'users.update'
    ));
    Route::get('/users/{user}', 'UserController@show')->name('users.show');

    /**
     * Relationship Routes
     */
    Route::get('/users/{user}/houses', 'UserController@allHouses')->name('users.houses');
    Route::get('/users/{user}/groups', 'UserController@allGroups')->name('users.groups');
    Route::get('/users/{user}/transactions', 'UserController@allTransactions')->name('users.transactions');
    Route::get('/users/{user}/pay', 'UserController@toPay')->name('users.toPay');
    Route::get('/users/{user}/receive', 'UserController@toReceive')->name('users.toReceive');
    Route::get('/users/{user}/paid', 'UserController@paid')->name('users.paid');
    Route::get('/users/{user}/received', 'UserController@received')->name('users.received');

//---------------------------------------------------------------


//---------------------------------------------------------------
// House Routes

    /**
     * Normal CRUD Routes
     */
    Route::get('/houses', 'HouseController@index')->name('houses.index');
    Route::post('/houses', 'HouseController@store')->name('houses.store');
    Route::delete('/houses/{house}', 'HouseController@destroy')->name('houses.destroy');
    Route::match(array('PUT', 'PATCH'), "/houses/{house}", array(
        'uses' => 'HouseController@update',
        'as' => 'houses.update'
    ));
    Route::get('/houses/{house}', 'HouseController@show')->name('houses.show');
    /**
     * Relationship Routes
     */
    Route::get('/houses/{house}/groups', 'HouseController@allGroups')->name('houses.groups');
    Route::get('/houses/{house}/users', 'HouseController@allUsers')->name('houses.users');
    Route::get('/houses/{house}/transactions', 'HouseController@allTransactions')->name('houses.transactions');
    /**
     * Collection Routes
     */
    Route::get('/houses/{house}/collection', 'HouseController@collection')->name('houses.collection');

//---------------------------------------------------------------




//---------------------------------------------------------------
// Group Routes

    /**
     * Normal CRUD Routes
     */
    Route::get('/groups', 'GroupController@index')->name('groups.index');
    Route::delete('/groups/{group}', 'GroupController@destroy')->name('groups.destroy');
    Route::post('/groups/{house}', 'GroupController@store')->name('groups.store');
    Route::match(array('PUT', 'PATCH'), "/groups/{group}", array(
        'uses' => 'GroupController@update',
        'as' => 'groups.update'
    ));
    Route::get('/groups/{group}', 'GroupController@show')->name('groups.show');

    /**
     * Relationship Routes
     */
    Route::get('/groups/{group}/users', 'GroupController@allUsers')->name('groups.users');
    Route::get('/groups/{group}/house', 'GroupController@house')->name('groups.house');
    Route::get('/groups/{group}/transactions', 'GroupController@allTransactions')->name('groups.transactions');
    Route::get('/groups/{group}/calculate', 'GroupController@calculatePayables')->name('groups.calculate');
    Route::get('/groups/{group}/payables', 'GroupController@allPayables')->name('groups.payables');
    Route::get('/groups/{group}/toPay', 'GroupController@toPay')->name('groups.toPay');
    Route::get('/groups/{group}/toRecieve', 'GroupController@toRecieve')->name('groups.toRecieve');

//---------------------------------------------------------------



//---------------------------------------------------------------
// Transaction Routes

    // Route::get('/transactions', 'TransactionController@index')->name('transactions.index');
    Route::post('/transactions/{group}', 'TransactionController@store')->name('transactions.store');
    Route::delete('/transactions/{transaction}', 'TransactionController@destroy')->name('transactions.destroy');
    Route::match(array('PUT', 'PATCH'), "/transactions/{transaction}", array(
        'uses' => 'TransactionController@update',
        'as' => 'transactions.update'
    ));
    Route::get('/transactions/{transaction}', 'TransactionController@show')->name('transactions.show');

//---------------------------------------------------------------


//---------------------------------------------------------------
// Payable Routes

    // Route::post('/payables', 'PayableController@store')->name('payables.store');
    // Route::delete('/payables/{payable}', 'PayableController@destroy')->name('payables.destroy');
    // Route::match(array('PUT', 'PATCH'), "/payables/{payable}", array(
    //     'uses' => 'PayableController@update',
    //     'as' => 'payables.update'
    // ));
    Route::get('/payables/{payable}', 'PayableController@show')->name('payables.show');

//---------------------------------------------------------------

});
