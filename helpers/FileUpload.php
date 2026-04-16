<?php

/**
 * File Upload Helper Class
 * Handles file upload operations with validation
 */

class FileUpload
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const MAX_FILE_SIZE = 5242880; // 5MB in bytes

    private $upload_directory;
    private $errors = [];

    /**
     * Constructor
     * 
     * @param string $upload_dir Upload directory path
     */
    public function __construct($upload_dir = UPLOAD_DIR)
    {
        $this->upload_directory = $upload_dir;

        // Create directory if it doesn't exist
        if (!is_dir($this->upload_directory)) {
            mkdir($this->upload_directory, 0755, true);
        }
    }

    /**
     * Upload file
     * 
     * @param array $file File from $_FILES
     * @return string|false Uploaded filename or false on failure
     */
    public function upload($file)
    {
        $this->errors = [];

        // Validate file
        if (!$this->validate_file($file)) {
            return false;
        }

        // Generate unique filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $unique_filename = uniqid('product_') . '.' . $file_extension;
        $destination = $this->upload_directory . $unique_filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $unique_filename;
        }

        $this->errors[] = "Failed to move uploaded file";
        return false;
    }

    /**
     * Validate uploaded file
     * 
     * @param array $file File from $_FILES
     * @return bool Validation status
     */
    private function validate_file($file)
    {
        // Check if file was uploaded
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "File upload error";
            return false;
        }

        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->errors[] = "File size exceeds maximum allowed size (5MB)";
            return false;
        }

        // Check file extension
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, self::ALLOWED_EXTENSIONS)) {
            $this->errors[] = "Invalid file type. Allowed: " . implode(', ', self::ALLOWED_EXTENSIONS);
            return false;
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mime_type, $allowed_mime_types)) {
            $this->errors[] = "Invalid file MIME type";
            return false;
        }

        return true;
    }

    /**
     * Get validation errors
     * 
     * @return array List of errors
     */
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * Delete file
     * 
     * @param string $filename Filename to delete
     * @return bool Success status
     */
    public function delete_file($filename)
    {
        $file_path = $this->upload_directory . $filename;

        if (file_exists($file_path)) {
            return unlink($file_path);
        }

        return false;
    }
}
