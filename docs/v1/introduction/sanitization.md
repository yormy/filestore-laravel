# File Types & Size
In the config you find a set of allowed filetypes, anything not allowed in that list will be denied
Also you can restrict the filesize to be accepted


# Sanitization of images

Santization:
- strips Exif from file
- Recompresses the page to 90%
- Stores as PNG

## Example
```
    $xid = UploadFileService::make($file)
        ->sanitize()
```

More advanced input checking handling you can find in our Tripwire package
