<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\Post;

use DailyFocus\Post\Application\GetFriendsPosts\GetFriendsPostsUseCase;
use DailyFocus\Post\Application\GetFriendsPosts\GetFriendsPostsCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class GetFriendsPostsController
{
    private GetFriendsPostsUseCase $getFriendsPostsUseCase;

    public function __construct(GetFriendsPostsUseCase $getFriendsPostsUseCase)
    {
        $this->getFriendsPostsUseCase = $getFriendsPostsUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $userId = $request->query->get('user_id');
            
            if (!$userId) {
                return new JsonResponse([
                    'error' => 'Missing required parameter: user_id'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new GetFriendsPostsCommand($userId);
            $posts = $this->getFriendsPostsUseCase->execute($command);

            $postsData = array_map(function($post) {
                return [
                    'id' => $post->id()->value(),
                    'author_id' => $post->authorId()->value(),
                    'content' => $post->content(),
                    'created_at' => $post->createdAt()->format('Y-m-d H:i:s'),
                    'archived_at' => $post->archivedAt()->format('Y-m-d H:i:s'),
                    'is_active' => $post->isActive()
                ];
            }, $posts);

            return new JsonResponse([
                'posts' => $postsData
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
