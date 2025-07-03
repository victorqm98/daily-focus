<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\User;

use DailyFocus\User\Application\RegisterUser\RegisterUserUseCase;
use DailyFocus\User\Application\RegisterUser\RegisterUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class RegisterUserController
{
    private RegisterUserUseCase $registerUserUseCase;

    public function __construct(RegisterUserUseCase $registerUserUseCase)
    {
        $this->registerUserUseCase = $registerUserUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['username'], $data['email'], $data['password'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: username, email, password'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new RegisterUserCommand(
                $data['username'],
                $data['email'],
                $data['password']
            );

            $this->registerUserUseCase->execute($command);

            return new JsonResponse([
                'message' => 'User registered successfully'
            ], Response::HTTP_CREATED);

        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
