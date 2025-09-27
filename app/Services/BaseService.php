<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseService
{
    /**
     * Execute a database transaction with proper error handling
     *
     * @param callable $callback
     * @param int $attempts
     * @return mixed
     * @throws Exception
     */
    protected function executeTransaction(callable $callback, int $attempts = 1)
    {
        return DB::transaction(function () use ($callback) {
            try {
                return $callback();
            } catch (Exception $e) {
                Log::error('Transaction failed', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'service' => static::class,
                    'user_id' => auth()->id(),
                ]);
                throw $e;
            }
        }, $attempts);
    }

    /**
     * Handle model creation with validation
     *
     * @param string $modelClass
     * @param array $data
     * @return Model
     * @throws Exception
     */
    protected function createModel(string $modelClass, array $data): Model
    {
        return $this->executeTransaction(function () use ($modelClass, $data) {
            $model = new $modelClass();
            $model->fill($data);

            if (!$model->save()) {
                throw new Exception("Failed to create {$modelClass}");
            }

            Log::info("Model created successfully", [
                'model' => $modelClass,
                'id' => $model->id,
                'user_id' => auth()->id(),
            ]);

            return $model->fresh();
        });
    }

    /**
     * Handle model update with validation
     *
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    protected function updateModel(Model $model, array $data): Model
    {
        return $this->executeTransaction(function () use ($model, $data) {
            $originalData = $model->getOriginal();
            $model->fill($data);

            if (!$model->save()) {
                throw new Exception("Failed to update " . get_class($model));
            }

            Log::info("Model updated successfully", [
                'model' => get_class($model),
                'id' => $model->id,
                'changes' => $model->getChanges(),
                'user_id' => auth()->id(),
            ]);

            return $model->fresh();
        });
    }

    /**
     * Handle model deletion with validation
     *
     * @param Model $model
     * @return bool
     * @throws Exception
     */
    protected function deleteModel(Model $model): bool
    {
        return $this->executeTransaction(function () use ($model) {
            $modelClass = get_class($model);
            $modelId = $model->id;

            if (!$model->delete()) {
                throw new Exception("Failed to delete {$modelClass}");
            }

            Log::info("Model deleted successfully", [
                'model' => $modelClass,
                'id' => $modelId,
                'user_id' => auth()->id(),
            ]);

            return true;
        });
    }

    /**
     * Validate file upload
     *
     * @param $file
     * @param array $allowedExtensions
     * @param int $maxSizeKB
     * @return bool
     * @throws Exception
     */
    protected function validateFile($file, array $allowedExtensions = [], int $maxSizeKB = 10240): bool
    {
        if (!$file || !$file->isValid()) {
            throw new Exception('Invalid file upload');
        }

        // Check file size
        if ($file->getSize() > ($maxSizeKB * 1024)) {
            throw new Exception("File size exceeds {$maxSizeKB}KB limit");
        }

        // Check file extension
        if (!empty($allowedExtensions)) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions));
            }
        }

        return true;
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @param array $rules
     * @return array
     */
    protected function sanitizeData(array $data, array $rules = []): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove potential XSS
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                $value = trim($value);
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /**
     * Get standardized success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    protected function successResponse($data = null, string $message = 'Operation completed successfully', int $statusCode = 200): array
    {
        $response = [
            'success' => true,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * Get standardized error response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return array
     */
    protected function errorResponse(string $message = 'An error occurred', int $statusCode = 500, array $errors = []): array
    {
        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    /**
     * Log activity for audit trail
     *
     * @param string $action
     * @param Model $model
     * @param array $details
     * @return void
     */
    protected function logActivity(string $action, Model $model, array $details = []): void
    {
        try {
            Log::info("User activity: {$action}", [
                'action' => $action,
                'model' => get_class($model),
                'model_id' => $model->id,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'unknown',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'details' => $details,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (Exception $e) {
            // Don't throw exception for logging failures
            Log::warning('Failed to log activity', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}