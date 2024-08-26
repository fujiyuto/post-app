<?php

namespace App\Http\Controllers;

use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    private $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function index_follow()
    {
        
    }

    public function index_follower()
    {

    }

    public function create()
    {

    }

    public function delete()
    {

    }
}
