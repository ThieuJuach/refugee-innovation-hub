# Image Upload Feature - User Guide

## Overview

The Refugee Innovation Hub now supports local image uploads from your device, in addition to providing image URLs.

## Features

- ✅ Upload images directly from your device
- ✅ Support for JPEG, PNG, GIF, and WebP formats
- ✅ Maximum file size: 5MB
- ✅ Image preview before submission
- ✅ Option to use image URL instead
- ✅ Automatic image optimization and storage

## How to Upload Images

### Method 1: Upload from Device (Recommended)

1. **Fill out the submission form**
2. **In the "Image" field:**
   - Click "Choose File" or "Browse"
   - Select an image from your device
   - Supported formats: JPEG, JPG, PNG, GIF, WebP
   - Maximum size: 5MB
3. **Preview your image:**
   - After selecting, you'll see a preview
   - Click "Remove Image" if you want to change it
4. **Submit the form**
   - The image will be uploaded automatically
   - You'll receive a confirmation message

### Method 2: Provide Image URL

1. **In the "Or provide Image URL" field:**
   - Enter a direct link to an image
   - Example: `https://example.com/image.jpg`
2. **Note:** If you provide a URL, any uploaded file will be ignored
3. **Submit the form**

## Image Requirements

### Supported Formats
- JPEG / JPG
- PNG
- GIF
- WebP

### Size Limits
- Maximum file size: **5MB**
- Recommended: Under 2MB for faster uploads

### Image Quality Tips
- Use high-quality images (but keep file size reasonable)
- Recommended dimensions: 1200x800 pixels
- Use JPEG for photos, PNG for graphics with transparency
- Compress images before uploading if they're too large

## File Storage

- Uploaded images are stored in: `/uploads/stories/`
- Files are automatically renamed with unique identifiers
- Original filenames are preserved in the database
- Images are accessible via URL: `/uploads/stories/filename.jpg`

## Security Features

- ✅ File type validation (only images allowed)
- ✅ File size limits (5MB maximum)
- ✅ Secure file naming (prevents conflicts)
- ✅ PHP execution blocked in uploads directory
- ✅ Directory listing disabled

## Troubleshooting

### "Invalid file type" Error
- **Problem:** File format not supported
- **Solution:** Convert image to JPEG, PNG, GIF, or WebP

### "File size exceeds 5MB" Error
- **Problem:** Image is too large
- **Solution:** 
  - Compress the image using an online tool
  - Resize the image to smaller dimensions
  - Use image editing software to reduce quality

### Image Not Displaying
- **Problem:** Image path incorrect or file missing
- **Solution:**
  - Check if file was uploaded successfully
  - Verify file permissions on server
  - Check browser console for errors

### Upload Fails
- **Problem:** Server configuration issue
- **Solution:**
  - Check PHP `upload_max_filesize` setting
  - Check PHP `post_max_size` setting
  - Verify `uploads/stories/` directory exists and is writable
  - Check server error logs

## Server Configuration

### PHP Settings Required

In `php.ini`, ensure these settings:

```ini
upload_max_filesize = 5M
post_max_size = 10M
file_uploads = On
```

### Directory Permissions

The `uploads/stories/` directory must be writable:

```bash
chmod 755 uploads/stories/
```

Or on Windows, ensure the folder has write permissions.

## For Administrators

### Managing Uploaded Images

1. **View uploaded images:**
   - Navigate to `uploads/stories/` directory
   - Images are stored with unique filenames

2. **Delete images:**
   - Remove files from `uploads/stories/` directory
   - Update database to remove image references

3. **Backup images:**
   - Regularly backup the `uploads/stories/` directory
   - Include in your backup strategy

### Database Storage

- Image URLs are stored in the `image_url` field
- Format: `/uploads/stories/filename.jpg`
- Full path: `http://yoursite.com/uploads/stories/filename.jpg`

## Best Practices

1. **Before Uploading:**
   - Optimize images for web (compress if needed)
   - Use appropriate dimensions (1200x800 recommended)
   - Choose the right format (JPEG for photos, PNG for graphics)

2. **File Naming:**
   - Original filenames are preserved in metadata
   - System generates unique filenames automatically
   - No need to rename files before uploading

3. **Storage:**
   - Regularly clean up unused images
   - Monitor disk space usage
   - Consider implementing image cleanup for rejected submissions

## Support

If you encounter issues with image uploads:

1. Check file format and size
2. Verify server configuration
3. Check browser console for errors
4. Contact your system administrator

---

**Last Updated:** November 2024

