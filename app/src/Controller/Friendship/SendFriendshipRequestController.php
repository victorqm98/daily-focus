<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\Friendship;

use DailyFocus\Friendship\Application\SendFriendshipRequest\SendFriendshipRequestUseCase;
use DailyFocus\Friendship\Application\SendFriendshipRequest\SendFriendshipRequestCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class SendFriendshipRequestController
{
    private SendFriendshipRequestUseCase $sendFriendshipRequestUseCase;

    public function __construct(SendFriendshipRequestUseCase $sendFriendshipRequestUseCase)
    {
        $this->sendFriendshipRequestUseCase = $sendFriendshipRequestUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['requester_id'], $data['addressee_id'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: requester_id, addressee_id'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new SendFriendshipRequestCommand(
                $data['requester_id'],
                $data['addressee_id']
            );

            $this->sendFriendshipRequestUseCase->execute($command);

            return new JsonResponse([
                'message' => 'Friendship request sent successfully'
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
