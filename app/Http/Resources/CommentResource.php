<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CommentResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        Carbon::setLocale('fr');
        return [
            'id' => $this->id,
            'content' => $this->content,
            'article_id' => $this->article_id,
            'user' => new UserResource($this->user),
            'date_creation' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
