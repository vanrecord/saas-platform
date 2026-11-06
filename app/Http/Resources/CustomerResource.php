<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = null;

    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'customer_group_id' => $this->customer_group_id,
                'is_default' => $this->is_default,
        ];
    }
}
