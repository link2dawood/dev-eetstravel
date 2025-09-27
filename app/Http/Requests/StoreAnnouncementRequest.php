<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow authenticated users to create announcements
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean and format input data
        $this->merge([
            'title' => trim($this->title ?? ''),
            'content' => trim($this->content ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Basic Information
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-Z0-9\s\-\.\,\!\?\:]+$/' // Allow letters, numbers, spaces, basic punctuation
            ],
            'content' => [
                'required',
                'string',
                'max:10000',
                'min:10'
            ],

            // Optional parent for replies
            'parent_id' => 'nullable|integer|exists:announcements,id',

            // File Uploads
            'files' => 'nullable|array|max:5', // Maximum 5 files
            'files.*' => [
                'file',
                'max:10240', // 10MB max per file
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt'
            ],

            // Media library files
            'announcement_files' => 'nullable|array|max:5',
            'announcement_files.*' => [
                'file',
                'max:10240',
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Announcement title is required.',
            'title.regex' => 'Title can only contain letters, numbers, spaces, and basic punctuation.',
            'title.min' => 'Title must be at least 3 characters long.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'content.required' => 'Announcement content is required.',
            'content.min' => 'Content must be at least 10 characters long.',
            'content.max' => 'Content cannot exceed 10,000 characters.',
            'parent_id.exists' => 'The parent announcement does not exist.',
            'files.max' => 'You can upload a maximum of 5 files.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Files must be of type: JPEG, JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT.',
            'announcement_files.max' => 'You can upload a maximum of 5 files.',
            'announcement_files.*.max' => 'Each file must not exceed 10MB.',
            'announcement_files.*.mimes' => 'Files must be of type: JPEG, JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'parent_id' => 'parent announcement',
            'announcement_files' => 'announcement files'
        ];
    }
}