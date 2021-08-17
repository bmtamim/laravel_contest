<?php


namespace App\DTO;


use App\Services\ContestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\DataTransferObject\DataTransferObject;

class ContestDTO extends DataTransferObject
{
    public $user_id;
    public $contest_no;
    public $title;
    public $description;
    public $short_description;
    public $competition_details;
    public $ticket_price;
    public $ticket_quantity;
    public $categories;
    public $image;
    public $competition_start;
    public $competition_end;
    public $status;
    public $image_gallery;

    public static function createFromRequest(Request $request): ContestDTO
    {

        $competition_date = explode('to', $request->input('competition_date'));
        $competition_start = Carbon::create(rtrim($competition_date[0]));
        $competition_end = Carbon::create(ltrim($competition_date[1]));
        $data = [
            'user_id'             => Auth::id(),
            'contest_no'          => $request->input('contest_no'),
            'title'               => $request->input('title'),
            'description'         => $request->input('description'),
            'short_description'   => $request->input('short_description'),
            'competition_details' => $request->input('competition_details'),
            'ticket_price'        => $request->input('ticket_price'),
            'ticket_quantity'     => $request->input('ticket_quantity'),
            'categories'          => $request->input('categories') ?? [1],
            'image'               => $request->file('image'),
            'competition_start'   => $competition_start,
            'competition_end'     => $competition_end,
            'status'              => $request->filled('status'),
            'image_gallery'       => $request->file('image_gallery'),
        ];
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'user_id'             => $this->user_id,
            'contest_no'          => $this->contest_no,
            'title'               => $this->title,
            'description'         => $this->description,
            'short_description'   => $this->short_description,
            'competition_details' => $this->competition_details,
            'ticket_price'        => $this->ticket_price,
            'ticket_quantity'     => $this->ticket_quantity,
            'image'               => $this->image,
            'categories'          => $this->categories,
            'competition_start'   => $this->competition_start,
            'competition_end'     => $this->competition_end,
            'image_gallery'       => $this->image_gallery,
            'status'              => $this->status,
        ];
    }

}
