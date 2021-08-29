<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

/**
 *Author: JP Lacsinto <jplacsinto@gmail.com>
 *Logic for fetching user details from gihub api
 */
class GithubController extends Controller
{
    const GITHUB_USERS = 'https://api.github.com/users';
    const CACHE_EXPIRATION = 120; //2 mins
    const MAX_USERS = 10;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users($usernames)
    {
        $usersArr = $promises = [];

        //instantiate client
        $client  = new \GuzzleHttp\Client(['http_errors' => false]);

        //get usernames and convert to array
        $usernames = explode(',', $usernames);
        $usernames = array_slice($usernames, 0, self::MAX_USERS);

        //loop thru all usernames
        //check data is available in cache, if yes skip requesting to github api
        foreach ($usernames as $username) {
            if (Cache::has($username)) {
                $usersArr[] = Cache::get($username);
                continue;
            }
            $promises[] = $client->getAsync(self::GITHUB_USERS."/{$username}");
        }

        //if requesting to gihub api, loop thru all each results
        //then store to cache
        if (!empty($promises)) {
            $promiseResponseArr = \GuzzleHttp\Promise\all($promises)->then(
                function (array $responses) {
                    $responseArr = [];
                    foreach ($responses as $response) {
                        $profile = json_decode($response->getBody(), true);
                        //skip if user not found
                        if (!isset($profile['login'])) {
                            continue;
                        }
                        $responseArr[] = Cache::remember($profile['login'], self::CACHE_EXPIRATION, function () use ($profile) {
                            return [
                                'name' => $profile['name'],
                                'login' => $profile['login'],
                                'company' => $profile['company'],
                                'followers' => $profile['followers'],
                                'public_repos' => $profile['public_repos'],
                                'average_public_repo_followers' => $profile['followers'] / $profile['public_repos']
                            ];
                        });
                    }
                    return $responseArr;
                }
            )->wait();

            //merge data from api results to cached results(if availble)
            $usersArr = array_merge($usersArr, $promiseResponseArr);
        }

        //sort results by name
        $sortKeys = array_column($usersArr, 'name');
        array_multisort($sortKeys, SORT_ASC, $usersArr);
        return response()->json($usersArr);
    }
}
