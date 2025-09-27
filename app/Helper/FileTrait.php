<?php
/**
 * @author: yurapif
 * Date: 24.05.2017
 */

namespace App\Helper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait FileTrait
{
    /**
     * add files to model
     *
     * @param Request $request
     * @param Model $obj
     */
    public function addFile(Request $request, Model $obj)
    {
        if (!is_null($request->file('attach'))){
            foreach ($request->file('attach') as $attach) {
                // Store the file first
                $path = $attach->store('uploads', 'public');

                $file_data = [
                    'attach_file_name' => $path,
                    'attach_file_size' => $attach->getSize(),
                    'attach_content_type' => $attach->getMimeType(),
                    'attach_updated_at' => now(),
                ];

                // Set the appropriate foreign key based on the model type
                $modelName = class_basename($obj);
                $foreignKey = strtolower($modelName) . '_id';
                $file_data[$foreignKey] = $obj->id;

                \App\File::create($file_data);
            };
        }

        if (!is_null($request->file('attach_bus'))){
            foreach ($request->file('attach_bus') as $attach) {
                // Store the file first
                $path = $attach->store('uploads', 'public');

                $file_data = [
                    'attach_file_name' => $path,
                    'attach_file_size' => $attach->getSize(),
                    'attach_content_type' => $attach->getMimeType(),
                    'attach_updated_at' => now(),
                ];

                // Set the appropriate foreign key based on the model type
                $modelName = class_basename($obj);
                $foreignKey = strtolower($modelName) . '_id';
                $file_data[$foreignKey] = $obj->id;

                \App\File::create($file_data);
            };
        }

        return;

    }

    /**
     * delete files
     * @param Model $model
     */
    public function removeFile(Model $model)
    {
        $files = $model->files()->get();
        foreach ($files as $file) {
            $file->delete();
        }
    }

    /**
     * deal with files type, images for images
     * @param Model $model
     *
     * @return array
     */
    public function parseAttach(Model $model): array
    {
        $image = [];
        $attach = [];
        foreach ($model->files as $file) {
            if ($file->attach_content_type == 'image/png' || $file->attach_content_type == 'image/jpeg') {
                array_push($image, $file);
            } else {
                array_push($attach, $file);
            }
        }

        return $files = ['image' => $image, 'attach' => $attach];
    }

    public function renderAttachmentsList(Model $model) : array
    {
        $files = $this->parseAttach($model);

        return view('component.attachments_list', compact('files'));
    }
}
