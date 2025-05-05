<?php 
declare(strict_types=1);

namespace Tests\Build;

use App\Services\Token\TokenService;
use Illuminate\Support\Facades\Redis;

class TokenCacheGenerate{



    public function __construct(private TokenService $token) {}

    public  function cacheToken(string $userID): string
    {
      $token= $this->token->generateToken($userID); 
      Redis::set($userID, $token);
      return $token;
    }
}