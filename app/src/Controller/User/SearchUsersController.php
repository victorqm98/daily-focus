<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\User;

use DailyFocus\User\Application\SearchUsers\SearchUsersUseCase;
use DailyFocus\User\Application\SearchUsers\SearchUsersCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchUsersController
{
    private SearchUsersUseCase $searchUsersUseCase;

    public function __construct(SearchUsersUseCase $searchUsersUseCase)
    {
        $this->searchUsersUseCase = $searchUsersUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $username = $request->query->get('username', '');
            
            if (empty($username)) {
                return new JsonResponse([
                    'error' => 'Username parameter is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new SearchUsersCommand($username);
            $users = $this->searchUsersUseCase->execute($command);

            $usersData = array_map(function($user) {
                return [
                    'id' => $user->id()->value(),
                    'username' => $user->username(),
                    'email' => $user->email()->value()
                ];
            }, $users);

            return new JsonResponse([
                'users' => $usersData
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
