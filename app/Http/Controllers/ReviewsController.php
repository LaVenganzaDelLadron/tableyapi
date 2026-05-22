<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewsRequest;
use App\Http\Requests\UpdateReviewsRequest;
use App\Models\Reviews;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    private const RELATIONS = ['user', 'product', 'order'];

    public function index(Request $request): JsonResponse
    {
        $reviews = Reviews::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Reviews retrieved successfully.', $reviews);
    }

    public function store(StoreReviewsRequest $request): JsonResponse
    {
        $review = Reviews::create($request->validated())->load(self::RELATIONS);

        return $this->success('Review created successfully.', $review, 201);
    }

    public function show(Reviews $review): JsonResponse
    {
        return $this->success('Review retrieved successfully.', $review->load(self::RELATIONS));
    }

    public function update(UpdateReviewsRequest $request, Reviews $review): JsonResponse
    {
        $review->update($request->validated());

        return $this->success('Review updated successfully.', $review->load(self::RELATIONS));
    }

    public function destroy(Reviews $review): JsonResponse
    {
        $review->delete();

        return $this->success('Review deleted successfully.');
    }
}
