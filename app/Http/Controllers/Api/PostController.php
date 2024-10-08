<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Info(title="API Documentation", version="1.0")
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get a list of posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of posts"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $posts = Post::paginate(10); // paginate 10 posts per page
        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Get a specific post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post data"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Post not found"
     *     )
     * )
     */
    public function show($id)
    {
        try
        {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $e)
        {
            return response()->json([
                'message' => 'Post not found'
            ], 400);
        }
        return response()->json($post);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Post Title"),
     *             @OA\Property(property="body", type="string", example="Post content here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try 
        {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required',
            ]);
        } catch (ValidationException $e) 
        {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }


        $post = Post::create([
            'user_id' => Auth::id(), // gets the currently authenticated user
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
        ]);

        return response()->json($post, 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/posts/{id}",
     *     summary="Update an existing post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Post Title"),
     *             @OA\Property(property="body", type="string", example="Updated post content here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try
        {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $e)
        {
            return response()->json([
                'message' => 'Post not found'
            ], 400);
        }

        try
        {
            $validatedData = $request->validate([
                'title' => 'sometimes|string|max:255',
                'body' => 'sometimes',
            ]);
        } catch (ValidationException $e)
        {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $post->update($validatedData);

        return response()->json($post);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Post not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try
        {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $e)
        {
            return response()->json([
                'message' => 'Post not found'
            ], 400);
        }
        $post->delete();

        return response()->json(null, 204);
    }
}
