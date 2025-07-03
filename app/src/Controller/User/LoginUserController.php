<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\User;

use DailyFocus\User\Application\LoginUser\LoginUserUseCase;
use DailyFocus\User\Application\LoginUser\LoginUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class LoginUserController
{
    private LoginUserUseCase $loginUserUseCase;

    public function __construct(LoginUserUseCase $loginUserUseCase)
    {
        $this->loginUserUseCase = $loginUserUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['email'], $data['password'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: email, password'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new LoginUserCommand(
                $data['email'],
                $data['password']
            );

            $user = $this->loginUserUseCase->execute($command);

            return new JsonResponse([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id()->value(),
                    'username' => $user->username(),
                    'email' => $user->email()->value()
                ]
            ], Response::HTTP_OK);

        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
