<?php

namespace NotificationSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates requests to send a notification from the dashboard.
 */
class SendNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'     => 'required|string|max:255',
            'body'      => 'required|string|max:5000',
            'targets'   => 'required|array|min:1',
            'targets.*' => 'string',
            'send_via'  => 'required|array|min:1',
            'send_via.*'=> 'string',
        ];
    }
}
