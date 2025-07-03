<?php

declare(strict_types=1);

namespace DailyFocus\App\Controller\Post;

use DailyFocus\Post\Application\CreatePost\CreatePostUseCase;
use DailyFocus\Post\Application\CreatePost\CreatePostCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

final class CreatePostController
{
    private CreatePostUseCase $createPostUseCase;

    public function __construct(CreatePostUseCase $createPostUseCase)
    {
        $this->createPostUseCase = $createPostUseCase;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['author_id'], $data['content'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: author_id, content'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new CreatePostCommand(
                $data['author_id'],
                $data['content']
            );

            $this->createPostUseCase->execute($command);

            return new JsonResponse([
                'message' => 'Post created successfully'
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
