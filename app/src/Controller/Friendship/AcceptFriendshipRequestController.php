<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\Friendship;

use DailyFocus\Friendship\Application\AcceptFriendshipRequest\AcceptFriendshipRequestUseCase;
use DailyFocus\Friendship\Application\AcceptFriendshipRequest\AcceptFriendshipRequestCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class AcceptFriendshipRequestController
{
    private AcceptFriendshipRequestUseCase $acceptFriendshipRequestUseCase;

    public function __construct(AcceptFriendshipRequestUseCase $acceptFriendshipRequestUseCase)
    {
        $this->acceptFriendshipRequestUseCase = $acceptFriendshipRequestUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['friendship_id'], $data['user_id'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: friendship_id, user_id'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new AcceptFriendshipRequestCommand(
                $data['friendship_id'],
                $data['user_id']
            );

            $this->acceptFriendshipRequestUseCase->execute($command);

            return new JsonResponse([
                'message' => 'Friendship request accepted successfully'
            ], Response::HTTP_OK);

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
